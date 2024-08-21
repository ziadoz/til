<?php
// @see: https://docs.pydantic.dev/latest/concepts/pydantic_settings/#usage

final readonly class DatabaseConfig
{
	#[Concat(
		#[Env('DB_HOST')],
		#[Str('://')],
		#[Env('DB_USER')],
		#[Str('@')],
		#[Env('DB_PASS', secret: true)] // Adds #[SensitiveParameter]???
	)]
	public string $dsn;
}

final readonly class AppConfig
{
	// Assumed from APP_NAME
	public string $appName;

	#[Env('APP_TIMEZONE')];
	public string $appTimezone;
}