# config/initializers/load_config.rb

config_file = "#{Rails.root}/config/config.yml"
APP_CONFIG = YAML.load_file(config_file)[Rails.env].with_indifferent_access if File.exists?(config_file)