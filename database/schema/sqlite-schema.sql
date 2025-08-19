CREATE TABLE IF NOT EXISTS "migrations"(
  "id" integer primary key autoincrement not null,
  "migration" varchar not null,
  "batch" integer not null
);
CREATE TABLE IF NOT EXISTS "users"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "email" varchar not null,
  "email_verified_at" datetime,
  "password" varchar not null,
  "team" varchar check("team" in('mobile', 'front_end', 'back_end', 'design', 'slt', 'e_commerce', 'bdm')) not null,
  "admin_status" varchar check("admin_status" in('yes', 'no')) not null,
  "keys_status" varchar check("keys_status" in('yes', 'no')) not null,
  "remember_token" varchar,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "users_email_unique" on "users"("email");
CREATE TABLE IF NOT EXISTS "password_reset_tokens"(
  "email" varchar not null,
  "token" varchar not null,
  "created_at" datetime,
  primary key("email")
);
CREATE TABLE IF NOT EXISTS "sessions"(
  "id" varchar not null,
  "user_id" integer,
  "ip_address" varchar,
  "user_agent" text,
  "payload" text not null,
  "last_activity" integer not null,
  primary key("id")
);
CREATE INDEX "sessions_user_id_index" on "sessions"("user_id");
CREATE INDEX "sessions_last_activity_index" on "sessions"("last_activity");
CREATE TABLE IF NOT EXISTS "cache"(
  "key" varchar not null,
  "value" text not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE TABLE IF NOT EXISTS "cache_locks"(
  "key" varchar not null,
  "owner" varchar not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE TABLE IF NOT EXISTS "jobs"(
  "id" integer primary key autoincrement not null,
  "queue" varchar not null,
  "payload" text not null,
  "attempts" integer not null,
  "reserved_at" integer,
  "available_at" integer not null,
  "created_at" integer not null
);
CREATE INDEX "jobs_queue_index" on "jobs"("queue");
CREATE TABLE IF NOT EXISTS "job_batches"(
  "id" varchar not null,
  "name" varchar not null,
  "total_jobs" integer not null,
  "pending_jobs" integer not null,
  "failed_jobs" integer not null,
  "failed_job_ids" text not null,
  "options" text,
  "cancelled_at" integer,
  "created_at" integer not null,
  "finished_at" integer,
  primary key("id")
);
CREATE TABLE IF NOT EXISTS "failed_jobs"(
  "id" integer primary key autoincrement not null,
  "uuid" varchar not null,
  "connection" text not null,
  "queue" text not null,
  "payload" text not null,
  "exception" text not null,
  "failed_at" datetime not null default CURRENT_TIMESTAMP
);
CREATE UNIQUE INDEX "failed_jobs_uuid_unique" on "failed_jobs"("uuid");
CREATE TABLE IF NOT EXISTS "personal_access_tokens"(
  "id" integer primary key autoincrement not null,
  "tokenable_type" varchar not null,
  "tokenable_id" integer not null,
  "name" varchar not null,
  "token" varchar not null,
  "abilities" text,
  "last_used_at" datetime,
  "expires_at" datetime,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "personal_access_tokens_tokenable_type_tokenable_id_index" on "personal_access_tokens"(
  "tokenable_type",
  "tokenable_id"
);
CREATE UNIQUE INDEX "personal_access_tokens_token_unique" on "personal_access_tokens"(
  "token"
);
CREATE TABLE IF NOT EXISTS "shifts"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "start_time" datetime not null,
  "end_time" datetime not null,
  "created_at" datetime,
  "updated_at" datetime,
  "type" varchar check("type" in('work', 'holiday', 'meeting')) not null default 'work',
  "location" varchar check("location" in('home', 'office', 'meeting')) not null default 'office',
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "notifications"(
  "id" integer primary key autoincrement not null,
  "type" varchar not null,
  "title" varchar not null,
  "message" text not null,
  "data" text,
  "is_read" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  "read_at" datetime
);
CREATE TABLE IF NOT EXISTS "teams"(
  "id" integer primary key autoincrement not null,
  "key" varchar not null,
  "name" varchar not null,
  "description" text,
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "teams_key_unique" on "teams"("key");

INSERT INTO migrations VALUES(6,'0001_01_01_000000_create_users_table',1);
INSERT INTO migrations VALUES(7,'0001_01_01_000001_create_cache_table',1);
INSERT INTO migrations VALUES(8,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO migrations VALUES(9,'2025_06_10_082447_create_personal_access_tokens_table',1);
INSERT INTO migrations VALUES(10,'2025_06_23_092730_shifts',1);
INSERT INTO migrations VALUES(11,'2025_07_08_102959_create_notifications_table',2);
INSERT INTO migrations VALUES(12,'2025_07_08_133607_update_notifications_table_structure',3);
INSERT INTO migrations VALUES(13,'2025_07_08_181009_create_teams_table',4);
INSERT INTO migrations VALUES(14,'2025_07_08_192305_add_type_to_shifts_table',5);
