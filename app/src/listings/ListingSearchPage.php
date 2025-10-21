<?php

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DateField;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\Core\Convert;

class ListingSearchPage extends Page
{
    private static $table_name = 'ListingSearchPage';
}

class ListingSearchPageController extends PageController
{
    private static $allowed_actions = [
        'FilterForm',
        'doFilter'
    ];

    public function Listings(): PaginatedList
    {
        $list = ListingPage::get()->filter('ShowInSearch', 1);
        $req  = $this->getRequest();

        // ----- location (Auckland area -> match against Address / SchoolZone) -----
        $area = $req->getVar('area');
        if ($area) {
            $areaSQL = Convert::raw2sql($area);
            $list = $list->filterAny([
                'Address:PartialMatch'    => $areaSQL,
                'SchoolZone:PartialMatch' => $areaSQL
            ]);
        }

        // ----- price (Cost) -----
        $min = $req->getVar('cost_min');
        $max = $req->getVar('cost_max');
        if ($min !== null && $min !== '') $list = $list->filter('Cost:GreaterThanOrEqual', (int)$min);
        if ($max !== null && $max !== '') $list = $list->filter('Cost:LessThanOrEqual', (int)$max);

        // ----- bedrooms / bathrooms / carparks -----
        $bed = $req->getVar('bedrooms');
        if ($bed !== null && $bed !== '') {
            $bed = (int)$bed;
            $list = ($bed >= 4) ? $list->filter('Bedrooms:GreaterThanOrEqual', 4) : $list->filter('Bedrooms', $bed);
        }
        $bath = $req->getVar('bathrooms');
        if ($bath !== null && $bath !== '') {
            $bath = (int)$bath;
            $list = ($bath >= 3) ? $list->filter('Bathrooms:GreaterThanOrEqual', 3) : $list->filter('Bathrooms', $bath);
        }
        $car = $req->getVar('carparks');
        if ($car !== null && $car !== '') $list = $list->filter('Carparks:GreaterThanOrEqual', (int)$car);

        // ----- floor space / land area -----
        $fsMin = $req->getVar('floorspace_min');
        $fsMax = $req->getVar('floorspace_max');
        $laMin = $req->getVar('landarea_min');
        $laMax = $req->getVar('landarea_max');
        if ($fsMin !== null && $fsMin !== '') $list = $list->filter('FloorSpace:GreaterThanOrEqual', (int)$fsMin);
        if ($fsMax !== null && $fsMax !== '') $list = $list->filter('FloorSpace:LessThanOrEqual', (int)$fsMax);
        if ($laMin !== null && $laMin !== '') $list = $list->filter('LandArea:GreaterThanOrEqual', (int)$laMin);
        if ($laMax !== null && $laMax !== '') $list = $list->filter('LandArea:LessThanOrEqual', (int)$laMax);

        // ----- created date range (Date) -----
        $createdFrom = $req->getVar('created_from'); // YYYY-MM-DD
        $createdTo   = $req->getVar('created_to');   // YYYY-MM-DD
        if ($createdFrom) $list = $list->filter('Date:GreaterThanOrEqual', $createdFrom);
        if ($createdTo)   $list = $list->filter('Date:LessThanOrEqual',   $createdTo);

        // ----- availability -----
        if ($req->getVar('avail') === '1') {
            $list = $list->filter('Availability', 1);
        }
        $availableBy = $req->getVar('available_by'); // YYYY-MM-DD
        if ($availableBy) {
            $dateSQL = Convert::raw2sql($availableBy);
            $list = $list->where(
                "\"Availability\" = 1 OR (\"DateAvailable\" IS NOT NULL AND \"DateAvailable\" <= '{$dateSQL}')"
            );
        }

        // ----- FEATURES (booleans) -----
        $this->applyBool($list, 'OpenPlan',        $req->getVar('openplan'));
        $this->applyBool($list, 'HasVideo',        $req->getVar('hasvideo'));
        $this->applyBool($list, 'IsFenced',        $req->getVar('fenced'));
        $this->applyBool($list, 'HasHeatPump',     $req->getVar('heatpump'));
        $this->applyBool($list, 'HasDeckArea',     $req->getVar('deck'));
        $this->applyBool($list, 'HasGardenArea',   $req->getVar('garden'));
        $this->applyBool($list, 'QualityAppliances',$req->getVar('qualityapps'));
        $this->applyBool($list, 'HasAC',           $req->getVar('ac'));
        $this->applyBool($list, 'IsFurnished',     $req->getVar('furnished'));

        // house type: mutually exclusive checkboxes OR dropdown
        $houseType = $req->getVar('housetype'); // '', 'house', 'townhouse'
        if ($houseType === 'house')     $list = $list->filter('HouseTypeHouse', 1);
        if ($houseType === 'townhouse') $list = $list->filter('HouseTypeTownhouse', 1);

        // paginate
        $paged = PaginatedList::create($list, $req);
        $paged->setPageLength(12);
        return $paged;
    }

    /**
     * Build the big filter form.
     */
    public function FilterForm(): Form
    {
        $fields = FieldList::create(
            // location
            DropdownField::create('area', 'Auckland area', $this->aucklandAreas())
                ->setEmptyString('(Any)'),

            // cost
            NumericField::create('cost_min', 'Min $/week'),
            NumericField::create('cost_max', 'Max $/week'),

            // counts
            DropdownField::create('bedrooms', 'Bedrooms', [
                ''  => '(Any)', '1'=>'1','2'=>'2','3'=>'3','4'=>'4+',
            ]),
            DropdownField::create('bathrooms', 'Bathrooms', [
                ''  => '(Any)', '1'=>'1','2'=>'2','3'=>'3+',
            ]),
            NumericField::create('carparks', 'Carparks (min)'),

            // sizes
            NumericField::create('floorspace_min', 'Floor space min (m²)'),
            NumericField::create('floorspace_max', 'Floor space max (m²)'),
            NumericField::create('landarea_min', 'Land area min (m²)'),
            NumericField::create('landarea_max', 'Land area max (m²)'),

            // created date range
            DateField::create('created_from', 'Created from')->setHTML5(true),
            DateField::create('created_to',   'Created to')->setHTML5(true),

            // availability
            CheckboxField::create('avail', 'Currently available'),
            DateField::create('available_by', 'Available by')->setHTML5(true)
                ->setDescription('Available now or by this date'),

            // FEATURES
            DropdownField::create('housetype', 'House type', [
                '' => '(Any)', 'house' => 'House', 'townhouse' => 'Townhouse'
            ]),
            CheckboxField::create('openplan',     'Open plan'),
            CheckboxField::create('hasvideo',     'Has video'),
            CheckboxField::create('fenced',       'Fully fenced'),
            CheckboxField::create('heatpump',     'Heat pump'),
            CheckboxField::create('deck',         'Deck area'),
            CheckboxField::create('garden',       'Garden area'),
            CheckboxField::create('qualityapps',  'Quality appliances'),
            CheckboxField::create('ac',           'Air conditioning'),
            CheckboxField::create('furnished',    'Fully furnished')
        );

        $actions = FieldList::create(
            FormAction::create('doFilter', 'Filter')
        );

        $form = Form::create($this, 'FilterForm', $fields, $actions);
        $form->setFormMethod('GET');
        $form->disableSecurityToken();
        $form->loadDataFrom($this->getRequest()->getVars());
        return $form;
    }

    public function doFilter($data, Form $form)
    {
        $url = $this->Link();
        $qs  = http_build_query(is_array($data) ? $data : []);
        return $this->redirect($url . ($qs ? '?' . $qs : ''));
    }

    // ---- helpers ----

    /**
     * Apply a boolean filter if the checkbox is '1'.
     */
    protected function applyBool(&$list, string $dbField, $var): void
    {
        if ($var === '1' || $var === 1 || $var === true) {
            $list = $list->filter($dbField, 1);
        }
    }

    /**
     * A small, opinionated list of common Auckland areas / suburbs.
     * You can expand or replace with your own taxonomy later.
     */
    protected function aucklandAreas(): array
    {
        $areas = [
            'Auckland CBD','Parnell','Newmarket','Epsom','Remuera','Mt Eden','Ponsonby',
            'Grey Lynn','Herne Bay','Onehunga','Mt Roskill','Sandringham','Avondale',
            'Henderson','Te Atatu','New Lynn','Massey','Hobsonville',
            'Takapuna','Devonport','Birkenhead','Northcote','Glenfield','Albany',
            'Orewa','Silverdale','Howick','Pakuranga','Botany Downs',
            'Manukau','Papatoetoe','Manurewa','Takanini','Papakura','Flat Bush','East Tamaki'
        ];
        // map to "value => label"
        return array_combine($areas, $areas);
    }
}
