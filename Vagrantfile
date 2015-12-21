# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|
  config.vm.box = "ubuntu/trusty64"
  config.vm.provision "shell", path: "provision.sh"

  config.vm.network "public_network"
  config.vm.network "private_network", ip: "192.168.33.10"
  config.vm.network "forwarded_port", host: 5100, guest: 80

  config.vm.synced_folder "./", "/var/www", id: "vagrant-www",
    owner: "vagrant",
    group: "www-data",
    mount_options: ["dmode=775,fmode=664"]
end