{ pkgs, lib, config, ... }:

{
  # PHP
  languages.php.enable = true;
  languages.php.version = lib.mkDefault "8.2";
  languages.php.fpm.pools.EL1280oS = lib.mkDefault {
    settings = {
      "clear_env" = "no";
      "pm" = "dynamic";
      "pm.max_children" = 10;
      "pm.start_servers" = 2;
      "pm.min_spare_servers" = 1;
      "pm.max_spare_servers" = 10;
    };
  };

  # Caddy
  services.caddy.enable = true;
  services.caddy.virtualHosts.":8592" = lib.mkDefault {
    extraConfig = lib.mkDefault ''
      root * public
      php_fastcgi unix/${config.languages.php.fpm.pools.EL1280oS.socket}
      file_server
    '';
  };
}
