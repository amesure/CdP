
CREATE TABLE `user` (
  `id_user` INT NOT NULL AUTO_INCREMENT,
  `login` VARCHAR(20),
  `email` VARCHAR(40),
  `password` VARCHAR(30),
  PRIMARY KEY  (`id_user`)
);

CREATE TABLE `project` (
  `id_project` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(100),
  `content` TEXT,
  `access` TEXT,
  PRIMARY KEY  (`id_project`)
);

CREATE TABLE `member` (
  `id_member` INT NOT NULL AUTO_INCREMENT,
  `id_user` INT,
  `id_project` INT,
  PRIMARY KEY (`id_member`)
);

CREATE TABLE `user_story` (
  `id_us` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(10),
  `content` TEXT,
  `cost` INT,
  PRIMARY KEY  (`id_us`)
);

CREATE TABLE `backlog` (
  `id_backlog` INT NOT NULL AUTO_INCREMENT,
  `id_project` INT,
  `id_us` INT,
  PRIMARY KEY  (`id_backlog`)
);

CREATE TABLE `sprint` (
  `id_sprint` INT NOT NULL AUTO_INCREMENT,
  `number` INT,
  `id_project` INT,
  `begin` DATE,
  `end` DATE,
  PRIMARY KEY  (`id_sprint`)
);

CREATE TABLE `sprint_us` (
  `id_sprint_us` INT NOT NULL AUTO_INCREMENT,
  `id_sprint` INT,
  `id_us` INT,
  PRIMARY KEY  (`id_sprint_us`)
);

CREATE TABLE `task` (
  `id_task` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(10),
  `content` TEXT,
  `cost` INT,
  `status` VARCHAR(20),
  `id_user` INT,
  PRIMARY KEY  (`id_task`)
);

CREATE TABLE `sprint_task` (
  `id_sprint_task` INT NOT NULL AUTO_INCREMENT,
  `id_sprint` INT,
  `id_task` INT,
  PRIMARY KEY  (`id_sprint_task`)
);

CREATE TABLE `dependancy` (
  `id_dependancy` INT NOT NULL AUTO_INCREMENT,
  `id_task1` INT,
  `id_task2` INT,
  PRIMARY KEY  (`id_dependancy`)
);



ALTER TABLE `member` ADD CONSTRAINT `member_fk1` FOREIGN KEY (`id_user`) REFERENCES user(`id_user`);
ALTER TABLE `member` ADD CONSTRAINT `member_fk2` FOREIGN KEY (`id_project`) REFERENCES project(`id_project`);

ALTER TABLE `backlog` ADD CONSTRAINT `backlog_fk1` FOREIGN KEY (`id_project`) REFERENCES project(`id_project`);
ALTER TABLE `backlog` ADD CONSTRAINT `backlog_fk2` FOREIGN KEY (`id_us`) REFERENCES user_story(`id_us`);
ALTER TABLE `sprint` ADD CONSTRAINT `sprint_fk1` FOREIGN KEY (`id_project`) REFERENCES project(`id_project`);
ALTER TABLE `sprint_us` ADD CONSTRAINT `sprint_us_fk1` FOREIGN KEY (`id_sprint`) REFERENCES sprint(`id_sprint`);
ALTER TABLE `sprint_us` ADD CONSTRAINT `sprint_us_fk2` FOREIGN KEY (`id_us`) REFERENCES user_story(`id_us`);
ALTER TABLE `task` ADD CONSTRAINT `task_fk1` FOREIGN KEY (`id_user`) REFERENCES user(`id_user`);
ALTER TABLE `sprint_task` ADD CONSTRAINT `sprint_task_fk1` FOREIGN KEY (`id_sprint`) REFERENCES sprint(`id_sprint`);
ALTER TABLE `sprint_task` ADD CONSTRAINT `sprint_task_fk2` FOREIGN KEY (`id_task`) REFERENCES task(`id_task`);
ALTER TABLE `dependancy` ADD CONSTRAINT `dependancy_fk1` FOREIGN KEY (`id_task1`) REFERENCES task(`id_task`);
ALTER TABLE `dependancy` ADD CONSTRAINT `dependancy_fk2` FOREIGN KEY (`id_task2`) REFERENCES task(`id_task`);