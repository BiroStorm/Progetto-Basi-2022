DROP DATABASE ProgettoBasi;

CREATE DATABASE ProgettoBasi;

USE ProgettoBasi;

CREATE TABLE Utente(
	Username varchar(24) PRIMARY KEY,
    Nome varchar(50) NOT NULL,
    Cognome varchar(50) NOT NULL,
    `Password` char(120), # controllare poi la lunghezza dell'hash generato...
    DataNascita DATE NOT NULL,
    LuogoNascita varchar(100) NOT NULL
);

CREATE TABLE Presenter(
	Username varchar(24) PRIMARY KEY,
    Foto varchar(100),
    Curriculum varchar(30),
    NomeUni varchar(50),
    Dipartimento varchar(50),
    FOREIGN KEY (Username) REFERENCES Utente(Username)
);

CREATE TABLE Speaker(
	Username varchar(24) PRIMARY KEY,
    Foto varchar(100),
    Curriculum varchar(30),
    NomeUni varchar(50),
    Dipartimento varchar(50),
    FOREIGN KEY (Username) REFERENCES Utente(Username)
);

CREATE TABLE Amministratore(
	Username varchar(24) PRIMARY KEY,
    FOREIGN KEY (username) REFERENCES Utente(Username)
);

CREATE TABLE Conferenza(
	Acronimo varchar(10),
    AnnoEdizione YEAR,
    Logo varchar(100),
    Nome varchar(150) NOT NULL,
    Svolgimento ENUM ("Attiva", "Completata") DEFAULT "Attiva",
    Totale_Sponsorizzazioni SMALLINT DEFAULT 0,
    DataInizio DATE,
    DataFine DATE,
    Creatore varchar(24),
    PRIMARY KEY (Acronimo, AnnoEdizione),
	FOREIGN KEY (Creatore) REFERENCES Amministratore(Username)
);

CREATE TABLE Sponsor(
	Nome varchar(50) PRIMARY KEY,
    Logo varchar(80)
);

CREATE TABLE Sponsorizzazione(
	NomeSponsor varchar(50),
    AcronimoConf varchar(10),
    AnnoEdizione YEAR,
    Importo int,
    PRIMARY KEY (NomeSponsor, AcronimoConf, AnnoEdizione),
    FOREIGN KEY (AcronimoConf, AnnoEdizione) REFERENCES Conferenza (Acronimo, AnnoEdizione),
    FOREIGN KEY (NomeSponsor) REFERENCES Sponsor(Nome)
);

CREATE TABLE Sessione(
	Codice INT AUTO_INCREMENT PRIMARY KEY,
    Link varchar(150),
    Titolo varchar(100),
    OraInizio TIME,
    OraFine TIME,
    Giorno DATE,
    AcronimoConf varchar(10),
    AnnoEdizione YEAR,
    FOREIGN KEY (AcronimoConf, AnnoEdizione) REFERENCES Conferenza (Acronimo, AnnoEdizione)
);

CREATE TABLE Presentazione(
	Codice INT AUTO_INCREMENT PRIMARY KEY,
    Titolo varchar(100) NOT NULL,
    OraFine TIME,
    OraInizio TIME,
    CodSessione INT,
    NumeroSequenza SMALLINT,
    FOREIGN KEY (CodSessione) REFERENCES Sessione(Codice)
);

CREATE TABLE Articolo(
	Codice INT PRIMARY KEY,
    Stato_Svolgimento ENUM ("Coperto", "Non Coperto"),
    NumeroPagine SMALLINT,
    `File` varchar(100), # path o poi vediamo...
    Presentatore varchar(24),
    FOREIGN KEY (Codice) REFERENCES Presentazione(Codice),
    FOREIGN KEY (Presentatore) REFERENCES Presenter(Username)
);

CREATE TABLE Tutorial(
	Codice INT PRIMARY KEY,
    Abstract varchar(500),
	FOREIGN KEY (Codice) REFERENCES Presentazione(Codice)
);

CREATE TABLE Insegnamento(
	CodiceTutorial INT,
    Username varchar(24),
    PRIMARY KEY(CodiceTutorial, Username),
	FOREIGN KEY (CodiceTutorial) REFERENCES Tutorial(Codice),
	FOREIGN KEY (Username) REFERENCES Speaker(Username)
);

CREATE TABLE Tag(
	Parola varchar(20),
    Articolo INT,
    PRIMARY KEY (Parola, Articolo),
    FOREIGN KEY (Articolo) REFERENCES Articolo(Codice)
);

CREATE TABLE Pubblicazione(
	CodiceArticolo INT,
    Nome varchar(50),
    Cognome varchar(50),
	FOREIGN KEY (CodiceArticolo) REFERENCES Articolo(Codice),
    PRIMARY KEY (CodiceArticolo, Nome, Cognome)
);

CREATE TABLE Associazione(
	UsernameAdmin varchar(24),
    AcronimoConf varchar(10),
    AnnoEdizione YEAR,
    FOREIGN KEY (AcronimoConf, AnnoEdizione) REFERENCES Conferenza (Acronimo, AnnoEdizione),
    FOREIGN KEY (UsernameAdmin) REFERENCES Amministratore(Username),
    PRIMARY KEY (UsernameAdmin, AcronimoConf, AnnoEdizione)
);

CREATE TABLE Registrazione(
	AnnoEdizione YEAR,
    AcronimoConf varchar(10),
    UsernameUtente varchar(24),
    PRIMARY KEY (AnnoEdizione, AcronimoConf, UsernameUtente),
    FOREIGN KEY (AcronimoConf, AnnoEdizione) REFERENCES Conferenza (Acronimo, AnnoEdizione),
    FOREIGN KEY (UsernameUtente) REFERENCES Utente(Username)
);

CREATE TABLE Preferiti(
	CodPresentazione INT,
    Username varchar(24),
    PRIMARY KEY (CodPresentazione, Username),
    FOREIGN KEY (CodPresentazione) REFERENCES Presentazione(Codice),
    FOREIGN KEY (Username) REFERENCES Utente(Username)
);

CREATE TABLE Risorsa(
	Link varchar(150),
    Descrizione varchar(255),
    CodTutorial INT,
    UsernameSpeaker varchar(24),
    FOREIGN KEY (CodTutorial) REFERENCES Tutorial(Codice),
    FOREIGN KEY (UsernameSpeaker) REFERENCES Speaker(Username),
    PRIMARY KEY (Link, CodTutorial)
);

CREATE TABLE Valutazione(
	CodPresentazione INT,
    UsernameAdmin varchar(24),
    Voto TINYINT NOT NULL,
    Note varchar(50),
    PRIMARY KEY (CodPresentazione, UsernameAdmin),
    FOREIGN KEY (CodPresentazione) REFERENCES Presentazione(Codice),
    FOREIGN KEY (UsernameAdmin) REFERENCES Amministratore(Username)
);

CREATE TABLE Messaggio(
	CodSessione INT,
    Mittente varchar(24),
    Orario TIME,
    Testo varchar(255) NOT NULL,
    PRIMARY KEY (CodSessione, Orario, Mittente),
    FOREIGN KEY (CodSessione) REFERENCES Sessione(Codice),
    FOREIGN KEY (Mittente) REFERENCES Utente(Username)
);


/* ---------------- STORED PROCEDURE --------------- */
DELIMITER |

# Controllo Credenziali per Login [0 = Login Fallito | 1 = Login Corretto]
CREATE PROCEDURE CheckLogin(IN UsernameI varchar(24), IN PasswordI char(120), OUT Registrato tinyint)
BEGIN
	SELECT COUNT(*) AS Registrato FROM Utente WHERE Username = UsernameI AND Passowrd = PasswordI;
END;
|

# Registrazione alla Piattaforma
CREATE PROCEDURE Registrazione(IN UsernameI varchar(24), IN PasswordI char(120), IN NomeI varchar(50),
	IN CognomeI varchar(50), IN DataNascita DATE, LuogoNascita varchar(100), OUT result varchar(25))
BEGIN
	DECLARE result varchar(25);
	START TRANSACTION;
		# Check if user with this username already exist.
		IF EXISTS(SELECT Username FROM Utente WHERE Username = UsernameI) THEN
			SET result = "Username occupato!";
		ELSE
			INSERT INTO Utente VALUES (UsernameI, NomeI, CognomeI, PasswordI, DataNascita, LuogoNascita);
			SET result = "Complete!";
        END IF;
    COMMIT WORK;
END;
|

# Visualizzazione Conferenze Disponibili [VisualAll ? Visualizza tutte : Visualizza solo quelle Attive]
CREATE PROCEDURE VisualizzaConferenze(IN VisualAll Bool)
BEGIN
	IF VisualAll THEN
		SELECT Acronimo, AnnoEdizione, Logo, Nome, Svolgimento, Totale_Sponsorizzazioni, Creatore
		FROM Conferenza;
	ELSE
		SELECT Acronimo, AnnoEdizione, Logo, Nome, Svolgimento, Totale_Sponsorizzazioni, Creatore
		FROM Conferenza WHERE Svolgimento = "Attiva";
	END IF;
END;
|

# REGISTRAZIONE AD UNA CONFERENZA
CREATE PROCEDURE IscrizioneConferenza(IN UsernameI varchar(24), IN AcronimoI varchar(10), IN Anno YEAR, OUT result varchar(25))
BEGIN
	DECLARE result varchar(25);
    START TRANSACTION;
		# Check if the user is already register in the Conference
        IF EXISTS(SELECT Username FROM Registrazione WHERE UsernameUtente = UsernameI AND AcronimoConf = AcronimoI AND AnnoEdizione = Anno) THEN
			SET result = "Utente già iscritto";
		ELSE 
			INSERT INTO Registrazione (UsernameUtente, AcronimoConf, AnnoEdizione) VALUES (UsernameI, AcronimoI, Anno);
			SET result = "Iscritto con successo!";
		END IF;
	COMMIT WORK;
END;
|

# Visualizzazione delle sessioni
CREATE PROCEDURE VisualizzazioneSessioni(IN AcronimoI varchar(10), IN Anno YEAR)
BEGIN
	SELECT Codice, Link, Titolo, OraInizio, OraFine, Giorno
    FROM Sessione WHERE AcronimoConf = AcronimoI AND AnnoEdizione = Anno
    ORDER BY Giorno, OraInizio, OraFine;
END;
|

# Visualizzazione Presentazioni data una Sessione
CREATE PROCEDURE VisualizzaPresentazioni(IN CodiceSessione INT)
BEGIN
	SELECT *
    FROM (
	SELECT P.Codice as "Codice", Titolo, OraInizio, OraFine, NumeroSequenza, "Articolo" as Tipologia
    FROM Presentazione P JOIN Articolo A
    ON P.Codice = A.Codice 
    WHERE P.CodSessione = CodiceSessione
    UNION
    SELECT P2.Codice as "Codice", Titolo, OraInizio, OraFine, NumeroSequenza, "Tutorial" as Tipologia
    FROM Presentazione P2 JOIN Tutorial T
    ON P2.Codice = T.Codice 
    WHERE P2.CodSessione = CodiceSessione
    ) AS T1
    ORDER BY NumeroSequenza, OraInizio, OraFine;
END;
|

# Visualizzazione messaggi nella chat di Sessione
CREATE PROCEDURE VisualizzaMessaggi(IN CodiceSessione INT)
BEGIN
	SELECT Mittente, Orario, Testo
    FROM Messaggio
    WHERE CodSessione = CodiceSessione
    ORDER BY Orario DESC 
    LIMIT 100;
END;
|

# Inserimento messaggi nella chat di Sessione
CREATE PROCEDURE InserisciMessaggio(IN CodSessione INT, IN Mittente varchar(24), IN Orario TIME, IN Testo varchar(255))
BEGIN
	START TRANSACTION;
		INSERT INTO Messaggio VALUES (CodSessione, Mittente, current_time(), Testo);
    COMMIT WORK;
END;
|

# Visualizzazione lista presentazioni favorite
CREATE PROCEDURE PresentazioniPreferite(IN UsernameUtente varchar(24))
BEGIN
	SELECT CodPresentazione, Titolo, OraInizio, OraFine, NumeroSequenza
	FROM Preferiti JOIN Presentazione
	ON CodPresentazione = Codice
	WHERE Username = UsernameUtente;
END;
|

# Inserimento presentazione Preferita
CREATE PROCEDURE InserisciPresentazionePreferita(IN UsernameUtente varchar(24), IN CodicePresentazione INT)
BEGIN
	# lasciamo gestire l'errore dal lato Server PHP
    INSERT INTO Preferiti VALUES (UsernameUtente, CodicePresentazione);
END;
|

########################## Operazioni per soli Amministratori ####################################
/* 	I controlli sull'Utente verranno effettuati a lato PHP, oltre alla sicurezza già garantita
	tramite i Vincoli di Integrità Inter-Relazionale.
*/

# Creazione di una Nuova Conferenza
CREATE PROCEDURE NuovaConferenza(IN UsernameUtenteI varchar(24), IN AcronimoI varchar(10), IN AnnoEdizioneI YEAR, IN LogoI varchar(100), IN NomeI varchar(150), IN Inizio DATE, IN Fine DATE)
BEGIN
	START TRANSACTION;
	INSERT INTO NuovaConferenza(Acronimo, AnnoEdizione, Logo, Nome, DataInizio, DataFine, Creatore) VALUES (AcronimoI, AnnoEdizioneI, LogoI, NomeI, Inizio, Fine, UsernameUtenteI);
	INSERT INTO Registrazione(UsernameUtente, AcronimoConf, AnnoEdizione) VALUES (UsernameUtenteI, AcronimoI, AnnoEdizioneI);
    # Si presume che quando un admin crea una nuova conferenza, oltre ad essere registrato ad essa, sia anche associato a tale conferenza.
	INSERT INTO Associazione(UsernameUtente, AcronimoConf, AnnoEdizione) VALUES (UsernameUtenteI, AcronimoI, AnnoEdizioneI);
    COMMIT WORK;
END;
|

# Creazione di una nuova sessione della conferenza
CREATE PROCEDURE NuovaSessione(IN LinkI varchar(150), IN TitoloI varchar(100), IN Inizio TIME, IN Fine TIME, IN GiornoI DATE, IN AcronimoConfI varchar(10), IN Anno YEAR, OUT result varchar(25))
BEGIN
	# Controllo che la Data sia compresa tra quella di Inizio e Fine della Conferenza.
	DECLARE result varchar(25);
    START TRANSACTION;
		IF GiornoI BETWEEN (Select DataInizio FROM Conferenza WHERE Acronimo = AcronimoConf AND AnnoEdizione = Anno) AND (Select DataFine FROM Conferenza WHERE Acronimo = AcronimoConf AND AnnoEdizione = Anno) THEN
			INSERT INTO Sessione(Link, Titolo, OraInizio, OraFine, Giorno, AcronimoConf, AnnoEdizione) VALUES (LinkI, TitoloI, Inizio, Fine, GiornoI, AcronimoConfI, Anno);
			SET result = "Complete!";
        ELSE
			SET result = "Errore Giorno Sessione!";
		END IF;
    COMMIT WORK;
END;
|

# Per la creazione di un Articolo si è deciso di non usare una Stored Procedure data la sua complessità.

# Creazione di un Tutorial
CREATE PROCEDURE NuovoTutorial(IN TitoloI varchar(100), IN Inizio TIME, IN Fine TIME, IN Sessione INT, IN Abstract varchar(500))
BEGIN
	# TODO NumeroSequenza????????
	INSERT INTO Presentazione(Titolo, OraInizio, OraFine, NumeroSequenza, CodSessione) VALUES (TitoloI, Inizio, Fine, Sessione);
END;
|

DELIMITER ; 

###################################  Viste as "Statistiche"  #####################################
CREATE VIEW NTotConferenze(Numero) AS (SELECT Count(*) FROM Conferenza);
CREATE VIEW NConferenzeAttive(Numero) AS (SELECT Count(*) FROM Conferenza WHERE Svolgimento = "Attiva");
CREATE VIEW NUtenti(Numero) AS (SELECT Count(*) FROM Utente);
CREATE VIEW ClassificaPresentazioni(t) AS (
	SELECT U.Username as "Username" , AVG(Voto) as "Media", U.Nome as "Nome", U.Cognome as "Cognome"
    FROM (SELECT Codice, Voto, Presentatore as "Username"
    FROM Valutazione V JOIN Articolo A 
    ON (V.CodPresentazione = A.Codice) 
    UNION
    SELECT Codice, Voto, I.Username as "Username"
    FROM Valutazione V JOIN Tutorial T JOIN Insegnamento I
    ON V.CodPresentazione = T.Codice AND T.Codice = I.CodiceTutorial) AS T1 JOIN Utente AS U
    ON T1.Username = U.Username
    GROUP BY U.Username
);


################################### Trigger #####################################
DELIMITER //
CREATE TRIGGER CambioStatoArticolo
AFTER UPDATE ON Articolo
FOR EACH ROW
BEGIN
	IF !(NEW.Presentatore <=> OLD.Presentatore) THEN
		SET NEW.stato_svolgimento = "Coperto";
	END IF;
END;
//

CREATE TRIGGER AggiornaNumeroPresentazioni
AFTER INSERT ON Presentazione
FOR EACH ROW
BEGIN
	UPDATE Sessione SET numero_presentazioni = (
		SELECT numero_presentazioni 
        FROM Sessione
        WHERE Codice = NEW.CodSessione)
	WHERE Codice = NEW.CodSessione;
END;
//
DELIMITER ;

################################ EVENTS ######################################
DELIMITER |
CREATE EVENT IF NOT EXISTS AggiornaStatoConferenza
ON SCHEDULE EVERY 1 DAY
	# Viene attivato ogni giorno a mezzanotte e 1 secondo.
	STARTS DATE_ADD(CURDATE(), INTERVAL 1 SECOND) ON COMPLETION PRESERVE 
DO 
BEGIN
	# in case of Error 1175 from MySQL Workbench: SET SQL_SAFE_UPDATES = 0;
	UPDATE Conferenza SET Svolgimento = "Completata" WHERE DataFine < CURDATE();
END
|
DELIMITER ;
