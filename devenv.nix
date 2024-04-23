{ pkgs, lib, config, ... }:

{
  # PHP
  languages.php.enable = true;
  languages.php.version = lib.mkDefault "8.3";
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
  languages.php.ini = ''
    memory_limit = 2G
    realpath_cache_ttl = 3600
    session.gc_probability = 0
    display_errors = On
    error_reporting = E_ALL
    assert.active = 0
    opcache.memory_consumption = 256M
    opcache.interned_strings_buffer = 20
    zend.assertions = 0
    short_open_tag = 0
    zend.detect_unicode = 0
    realpath_cache_ttl = 3600
  '';

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
