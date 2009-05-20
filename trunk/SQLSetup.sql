CREATE TABLE IF NOT EXISTS aliases (
  original varchar(4) NOT NULL,
  alias varchar(4) NOT NULL,
  description varchar(32) NOT NULL,
  PRIMARY KEY (original,alias)
) ;

INSERT INTO aliases (original, alias, description) VALUES ('Eevi', 'EEVM', 'Soul Keeper');
INSERT INTO aliases (original, alias, description) VALUES ('H00R', 'H07I', 'Undying');
INSERT INTO aliases (original, alias, description) VALUES ('Hblm', 'H06W', 'Keeper of the Light');
INSERT INTO aliases (original, alias, description) VALUES ('Hblm', 'H06X', 'Keeper of the Light');
INSERT INTO aliases (original, alias, description) VALUES ('Hblm', 'H06Y', 'Keeper of the Light');
INSERT INTO aliases (original, alias, description) VALUES ('Hlgr', 'H00E', 'Dragon Knight');
INSERT INTO aliases (original, alias, description) VALUES ('Hlgr', 'H00G', 'Dragon Knight');
INSERT INTO aliases (original, alias, description) VALUES ('N016', 'N02B', 'Troll Warlord');
INSERT INTO aliases (original, alias, description) VALUES ('N01I', 'N01J', 'Alchemist');
INSERT INTO aliases (original, alias, description) VALUES ('N01O', 'N015', 'Lone Druid');
INSERT INTO aliases (original, alias, description) VALUES ('U008', 'E015', 'Lycanthrope');

CREATE TABLE IF NOT EXISTS originals (
  original varchar(4) NOT NULL,
  description varchar(32) NOT NULL,
  PRIMARY KEY (original)
);

INSERT INTO originals (original, description) VALUES ('E002', 'Lightning Revenant');
INSERT INTO originals (original, description) VALUES ('E004', 'Bone Fletcher');
INSERT INTO originals (original, description) VALUES ('E005', 'Moon Rider');
INSERT INTO originals (original, description) VALUES ('E00P', 'Twin Head Dragon');
INSERT INTO originals (original, description) VALUES ('E015', 'Lycanthrope');
INSERT INTO originals (original, description) VALUES ('E01A', 'Witch Doctor');
INSERT INTO originals (original, description) VALUES ('E01B', 'Spectre');
INSERT INTO originals (original, description) VALUES ('E01C', 'Warlock');
INSERT INTO originals (original, description) VALUES ('E01Y', 'Templar Assassin');
INSERT INTO originals (original, description) VALUES ('EC45', 'Faceless Void');
INSERT INTO originals (original, description) VALUES ('EC57', 'Venomancer');
INSERT INTO originals (original, description) VALUES ('EC77', 'Netherdrake');
INSERT INTO originals (original, description) VALUES ('Edem', 'Anti-Mage');
INSERT INTO originals (original, description) VALUES ('Eevi', 'Soul Keeper');
INSERT INTO originals (original, description) VALUES ('EEVM', 'Soul Keeper');
INSERT INTO originals (original, description) VALUES ('Ekee', 'Tormented Soul');
INSERT INTO originals (original, description) VALUES ('Emns', 'Prophet');
INSERT INTO originals (original, description) VALUES ('Emoo', 'Enchantress');
INSERT INTO originals (original, description) VALUES ('Ewar', 'Phantom Assassin');
INSERT INTO originals (original, description) VALUES ('H000', 'Centaur Warchief');
INSERT INTO originals (original, description) VALUES ('H001', 'Rogue Knight');
INSERT INTO originals (original, description) VALUES ('H004', 'Slayer');
INSERT INTO originals (original, description) VALUES ('H008', 'Bristleback');
INSERT INTO originals (original, description) VALUES ('H00A', 'Holy Knight');
INSERT INTO originals (original, description) VALUES ('H00D', 'Beastmaster');
INSERT INTO originals (original, description) VALUES ('H00E', 'Dragon Knight');
INSERT INTO originals (original, description) VALUES ('H00G', 'Dragon Knight');
INSERT INTO originals (original, description) VALUES ('H00H', 'Oblivion');
INSERT INTO originals (original, description) VALUES ('H00I', 'Geomancer');
INSERT INTO originals (original, description) VALUES ('H00K', 'Goblin Techies');
INSERT INTO originals (original, description) VALUES ('H00N', 'Dark Seer');
INSERT INTO originals (original, description) VALUES ('H00Q', 'Sacred Warrior');
INSERT INTO originals (original, description) VALUES ('H00R', 'Undying');
INSERT INTO originals (original, description) VALUES ('H00S', 'Storm Spirit');
INSERT INTO originals (original, description) VALUES ('H00T', 'Clockwerk Goblin');
INSERT INTO originals (original, description) VALUES ('H00U', 'Invoker');
INSERT INTO originals (original, description) VALUES ('H00V', 'Gorgon');
INSERT INTO originals (original, description) VALUES ('H06S', 'Admiral Proudmoore');
INSERT INTO originals (original, description) VALUES ('H06W', 'Keeper of the Light');
INSERT INTO originals (original, description) VALUES ('H06X', 'Keeper of the Light');
INSERT INTO originals (original, description) VALUES ('H06Y', 'Keeper of the Light');
INSERT INTO originals (original, description) VALUES ('H07I', 'Undying');
INSERT INTO originals (original, description) VALUES ('Hamg', 'Treant Protector');
INSERT INTO originals (original, description) VALUES ('Harf', 'Omniknight');
INSERT INTO originals (original, description) VALUES ('Hblm', 'Keeper of the Light');
INSERT INTO originals (original, description) VALUES ('HC49', 'Naga Siren');
INSERT INTO originals (original, description) VALUES ('HC92', 'Stealth Assassin');
INSERT INTO originals (original, description) VALUES ('Hjai', 'Crystal Maiden');
INSERT INTO originals (original, description) VALUES ('Hlgr', 'Dragon Knight');
INSERT INTO originals (original, description) VALUES ('Hmbr', 'Lord of Olympia');
INSERT INTO originals (original, description) VALUES ('Hmkg', 'Ogre Magi');
INSERT INTO originals (original, description) VALUES ('Huth', 'Ursa Warrior');
INSERT INTO originals (original, description) VALUES ('Hvsh', 'Bloodseeker');
INSERT INTO originals (original, description) VALUES ('Hvwd', 'Vengeful Spirit');
INSERT INTO originals (original, description) VALUES ('N00B', 'Faerie Dragon');
INSERT INTO originals (original, description) VALUES ('N00R', 'Pit Lord');
INSERT INTO originals (original, description) VALUES ('N015', 'Lone Druid');
INSERT INTO originals (original, description) VALUES ('N016', 'Troll Warlord');
INSERT INTO originals (original, description) VALUES ('N01A', 'Silencer');
INSERT INTO originals (original, description) VALUES ('N01I', 'Alchemist');
INSERT INTO originals (original, description) VALUES ('N01J', 'Alchemist');
INSERT INTO originals (original, description) VALUES ('N01O', 'Lone Druid');
INSERT INTO originals (original, description) VALUES ('N01V', 'Priestess of the Moon');
INSERT INTO originals (original, description) VALUES ('N01W', 'Shadow Priest');
INSERT INTO originals (original, description) VALUES ('N02B', 'Troll Warlord');
INSERT INTO originals (original, description) VALUES ('N0EG', 'Windrunner');
INSERT INTO originals (original, description) VALUES ('Naka', 'Bounty Hunter');
INSERT INTO originals (original, description) VALUES ('Nbbc', 'Juggernaut');
INSERT INTO originals (original, description) VALUES ('Nbrn', 'Drow Ranger');
INSERT INTO originals (original, description) VALUES ('NC00', 'Skeleton King');
INSERT INTO originals (original, description) VALUES ('Nfir', 'Shadow Fiend');
INSERT INTO originals (original, description) VALUES ('Npbm', 'Pandaren Brewmaster');
INSERT INTO originals (original, description) VALUES ('Ntin', 'Tinker');
INSERT INTO originals (original, description) VALUES ('O00J', 'Spiritbreaker');
INSERT INTO originals (original, description) VALUES ('O00P', 'Morphling');
INSERT INTO originals (original, description) VALUES ('Ofar', 'Tidehunter');
INSERT INTO originals (original, description) VALUES ('Ogrh', 'Phantom Lancer');
INSERT INTO originals (original, description) VALUES ('Opgh', 'Axe');
INSERT INTO originals (original, description) VALUES ('Orkn', 'Shadow Shaman');
INSERT INTO originals (original, description) VALUES ('Oshd', 'Bane Elemental');
INSERT INTO originals (original, description) VALUES ('Otch', 'Earthshaker');
INSERT INTO originals (original, description) VALUES ('U000', 'Nerubian Assassin');
INSERT INTO originals (original, description) VALUES ('U006', 'Broodmother');
INSERT INTO originals (original, description) VALUES ('U008', 'Lycanthrope');
INSERT INTO originals (original, description) VALUES ('U00A', 'Chaos Knight');
INSERT INTO originals (original, description) VALUES ('U00C', 'Lifestealer');
INSERT INTO originals (original, description) VALUES ('U00E', 'Necrolyte');
INSERT INTO originals (original, description) VALUES ('U00F', 'Butcher');
INSERT INTO originals (original, description) VALUES ('U00K', 'Sand King');
INSERT INTO originals (original, description) VALUES ('U00P', 'Obsidian Destroyer');
INSERT INTO originals (original, description) VALUES ('Ubal', 'Nerubian Weaver');
INSERT INTO originals (original, description) VALUES ('UC01', 'Queen of Pain');
INSERT INTO originals (original, description) VALUES ('UC11', 'Magnataur');
INSERT INTO originals (original, description) VALUES ('UC18', 'Demon Witch');
INSERT INTO originals (original, description) VALUES ('UC42', 'Doom Bringer');
INSERT INTO originals (original, description) VALUES ('UC60', 'Necro''lic');
INSERT INTO originals (original, description) VALUES ('UC76', 'Death Prophet');
INSERT INTO originals (original, description) VALUES ('UC91', 'Slithereen Guard');
INSERT INTO originals (original, description) VALUES ('Ucrl', 'Stone Giant');
INSERT INTO originals (original, description) VALUES ('Udea', 'Lord of Avernus');
INSERT INTO originals (original, description) VALUES ('Udre', 'Night Stalker');
INSERT INTO originals (original, description) VALUES ('Uktl', 'Enigma');
INSERT INTO originals (original, description) VALUES ('Ulic', 'Lich');
INSERT INTO originals (original, description) VALUES ('Usyl', 'Dwarven Sniper');

CREATE TABLE IF NOT EXISTS scores (
	id INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL , 
	category VARCHAR, 
	name VARCHAR, 
	server VARCHAR, 
	score DOUBLE
);