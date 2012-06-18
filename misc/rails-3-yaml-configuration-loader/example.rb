# Access via symbol or string
puts APP_CONFIG[:key]
puts APP_CONFIG['key']

# Symbolize all keys when using to config other Rails components
ActionMailer::Base.smtp_settings = APP_CONFIG[:mailer][:smtp_settings].symbolize_keys
ActionMailer::Base.default APP_CONFIG[:mailer][:defaults].symbolize_keys