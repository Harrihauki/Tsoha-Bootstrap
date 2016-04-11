Create table RegisteredUser (
	id SERIAL PRIMARY KEY,
	username VARCHAR(22) NOT NULL,
	password VARCHAR(22) NOT NULL
);

Create table League (
	id SERIAL PRIMARY KEY,
	name VARCHAR(22) NOT NULL
);

Create table Team (
	id SERIAL PRIMARY KEY,
	name VARCHAR(22) NOT NULL,
        elo INTEGER,
	league_id INTEGER REFERENCES League(id)
);

Create table Match (
	id SERIAL PRIMARY KEY,
	home_id INTEGER REFERENCES Team(id),
	away_id INTEGER REFERENCES Team(id),
	date DATE NOT NULL,
	home_goals INTEGER,
	away_goals INTEGER,
	adder_id INTEGER REFERENCES RegisteredUser(id),
        home_elo_before INTEGER,
        away_elo_before INTEGER,
        home_elo_after INTEGER,
        away_elo_after INTEGER
);
