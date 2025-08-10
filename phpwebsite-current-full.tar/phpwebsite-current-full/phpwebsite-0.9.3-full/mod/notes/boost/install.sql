CREATE TABLE mod_notes (
  id int PRIMARY KEY,
  toUser varchar(20),
  toGroup varchar(30),
  fromUser varchar(20) NOT NULL,
  message text NOT NULL,
  dateSent datetime NOT NULL,
  dateRead datetime NOT NULL,
  userRead varchar(20)
);
