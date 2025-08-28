Vagrant.configure("2") do |config|
  config.vm.box = "ubuntu/jammy64"

  config.vm.network "private_network", ip: "192.168.56.10"

  config.vm.synced_folder ".", "/var/www/html"

  config.vm.provision "shell", inline: <<-SHELL
    # Update packages
    apt-get update

    # Install PHP 8.1 and modules
    apt-get install -y php php-cli php-common php-mysql php-xml php-mbstring php-curl \
    php-zip php-bcmath php-intl libapache2-mod-php unzip git

    # Install MySQL
    DEBIAN_FRONTEND=noninteractive apt-get install -y mysql-server

    # Configure MySQL user and database
    mysql -e "CREATE DATABASE silverstripe;"
    mysql -e "CREATE USER 'ssuser'@'localhost' IDENTIFIED BY 'sspassword';"
    mysql -e "GRANT ALL PRIVILEGES ON silverstripe.* TO 'ssuser'@'localhost';"
    mysql -e "FLUSH PRIVILEGES;"

    # Enable Apache mod_rewrite
    a2enmod rewrite
    systemctl restart apache2
  SHELL
end
