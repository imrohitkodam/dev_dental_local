CREATE TABLE IF NOT EXISTS "#__jfbconnect_autopost" (
	"id" SERIAL,
	"channel_id" INT NOT NULL,
	"opengraph_type" TEXT,
	"created" TIMESTAMP NOT NULL,
	PRIMARY KEY ("id")
);

CREATE TABLE IF NOT EXISTS "#__jfbconnect_autopost_activity" (
	"id" SERIAL,
	"autopost_id" INT NOT NULL,
	"type" SMALLINT NOT NULL,
	"url" text,
	"provider" text,
	"channel_type" text,
	"ext" text,
	"layout" text,
	"item_id" INT NOT NULL,
	"status" SMALLINT NOT NULL,
	"response" text,
	"created" TIMESTAMP NOT NULL,
	PRIMARY KEY ("id")
);