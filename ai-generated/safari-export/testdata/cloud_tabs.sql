CREATE TABLE cloud_tab_devices (
    device_uuid               TEXT PRIMARY KEY,
    system_fields             BLOB NOT NULL DEFAULT '',
    device_name               TEXT,
    device_type_identifier    TEXT,
    has_duplicate_device_name BOOLEAN DEFAULT 0,
    is_ephemeral_device       BOOLEAN DEFAULT 0,
    last_modified             REAL NOT NULL DEFAULT 0
);

CREATE TABLE cloud_tabs (
    tab_uuid                          TEXT PRIMARY KEY,
    system_fields                     BLOB NOT NULL DEFAULT '',
    device_uuid                       TEXT NOT NULL,
    position                          BLOB NOT NULL DEFAULT '',
    title                             TEXT,
    url                               TEXT NOT NULL,
    is_showing_reader                 BOOLEAN DEFAULT 0,
    is_pinned                         BOOLEAN DEFAULT 0,
    reader_scroll_position_page_index INTEGER DEFAULT 0,
    scene_id                          TEXT,
    last_viewed_time                  REAL DEFAULT 0
);

INSERT INTO cloud_tab_devices (device_uuid, device_name) VALUES
    ('device-1', 'iPhone'),
    ('device-2', 'iPad');

INSERT INTO cloud_tabs (tab_uuid, device_uuid, title, url, last_viewed_time) VALUES
    ('tab-1', 'device-1', 'Go Blog', 'https://go.dev/blog',  775825472.0),
    ('tab-2', 'device-1', 'GitHub',  'https://github.com',   775825000.0),
    ('tab-3', 'device-2', 'Apple',   'https://apple.com',    775800000.0),
    ('tab-4', 'device-2', '',        '',                      775800000.0);
