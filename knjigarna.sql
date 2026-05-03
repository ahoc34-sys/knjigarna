-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Gostitelj: localhost:8889
-- Čas nastanka: 03. maj 2026 ob 15.31
-- Različica strežnika: 8.0.44
-- Različica PHP: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Zbirka podatkov: `knjigarna`
--

-- --------------------------------------------------------

--
-- Struktura tabele `Book`
--

CREATE TABLE `Book` (
  `BookID` int NOT NULL,
  `Name` varchar(200) NOT NULL,
  `Author` varchar(100) NOT NULL,
  `Description` text,
  `Content` longtext,
  `BookCover` varchar(255) DEFAULT NULL,
  `BookCategoryID` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Odloži podatke za tabelo `Book`
--

INSERT INTO `Book` (`BookID`, `Name`, `Author`, `Description`, `Content`, `BookCover`, `BookCategoryID`) VALUES
(1, 'Harry Potter in Kamen modrosti', 'J.K. Rowling', 'Dečko ki je preživel odkrije čarobni svet Hogwartsa.', 'Harry Potter je živel pri svojih tetki in stricu Dursleyevih, ki so ga nikoli zares sprejeli. Na njegov enajsti rojstni dan pa prispe skrivnostno pismo, ki spremeni vse. Harry izve, da je čarovnik in da je sprejet na šolo za čarovništvo Hogwarts. Tam spozna prijatelje Rona in Hermiono ter se prvič sooči s skrivnostjo Kamna modrosti, ki ga želi v svoje roke dobiti temni čarovnik.', 'cover_69f5c12ddf3f7.jpg', 4),
(2, 'Hobbit', 'J.R.R. Tolkien', 'Bilbo Baggins se poda na nepričakovano pustolovščino.', 'Bilbo Baggins je udoben hobbit, ki ne mara pustolovščin. Toda nekega dne k njemu prispe čarovnik Gandalf z družino trinajstih škratov. Skupaj se podajo na dolgo pot, da bi si povrnili zaklad, ki ga varuje zmaj Smaug. Na poti Bilbo najde skrivnosten prstan, ki mu bo v prihodnje spremenil življenje.', 'cover_69f5c135bd9de.jpg', 4),
(3, 'Ubiti ptičo oponašalko', 'Harper Lee', 'Zgodba o rasni krivici in nedolžnosti v ameriškem Jugu.', 'V majhnem mestecu Maycomb v Alabami odvetnik Atticus Finch zagovarja temnopoltega moškega, obtoženim posilstva bele ženske. Zgodbo pripoveduje njegova hčerka Scout, ki skozi otroške oči doživlja nepravičnosti odraslega sveta. Roman je eden najpomembnejših del ameriške literature 20. stoletja.', 'cover_69f5c1a9e5a0b.jpg', 1),
(4, 'Mojster in Margareta', 'Mihail Bulgakov', 'Hudič obišče Moskvo in povzroči kaos.', 'Roman se odvija na dveh ravneh: v starobabilonski Jeruzalemu, kjer Pontij Pilat obsodi Ješua Ha-Nocri, in v Moskvi tridesetih let, kamor prispe skrivnostni profesor Woland s svojo čudno druščino. Satira, ljubezenska zgodba in filozofski roman v enem.', 'cover_69f5c179d1b0e.jpg', 1),
(5, 'Zločin in kazen', 'Fjodor Dostojevski', 'Psihološki roman o moralnih posledicah umora.', 'Rodion Raskolnikov, revni študent v Sankt Peterburgu, prepriča samega sebe, da ima pravico ubiti pohlepno oderuhačico. Po dejanju ga začne razjedati krivda in psihološki zlom. Roman je eno najprodornejših del svetovne literature o morali, krivdi in odkupitvi.', 'cover_69f5c1b47bef2.jpg', 3),
(6, 'Mali princ', 'Antoine de Saint-Exupéry', 'Poetična pravljica o otroku z asteroida B-612.', 'Pilot prisili na sredo Sahare. Tam spozna malega princa, ki prihaja z majhnega asteroida. Skozi njun pogovor spoznamo resnice o ljubezni, prijateljstvu, odgovornosti in o tem, kaj v življenju resnično šteje. Klasika svetovne literature, ki nagovarja tako otroke kot odrasle.', 'cover_69f5c16843bb5.jpg', 2),
(7, 'Sto let samote', 'Gabriel García Márquez', 'Saga o sedmih generacijah družine Buendía.', 'Ustanovitev mesta Macondo in usoda družine Buendía skozi sedem generacij. Roman je temeljno delo magičnega realizma, kjer se čudežno prepleta z vsakdanjim. Marquez nam prikaže vzpon in propad civilizacije, ljubezni, vojne in pozabe.', 'cover_69f5c19bdabe5.jpg', 1),
(8, 'Deklina zgodba', 'Margaret Atwood', 'Distopični roman o totalitarni republiki Gilead.', 'V bližnji prihodnosti je Amerika postala totalitarna teokracija Gilead. Ženske so izgubile vse pravice. Offred je dekla, namenjena rojevanju otrok za vladajoče elite. Njen edini upor je pripovedovanje zgodbe, ki jo skriva v sebi. Eden najpomembnejših distopičnih romanov.', 'cover_69f5c11eb830f.jpg', 1),
(9, 'Sherlock Holmes: Avanture', 'Arthur Conan Doyle', 'Zbirka kratkih zgodb o slavnem detektivu.', 'Zbirka dvanajstih kratkih zgodb o Sherlocku Holmesu in njegovem prijatelju dr. Watsonu. Holmes z nenavadnimi metodami dedukCije rešuje primere, ki so preveč zapleteni za Scotland Yard. Klasika detektivskega žanra, ki je zaznamovala celotno kriminalistično literaturo.', 'cover_69f5c27212fd7.jpg', 3),
(10, 'Igra prestola', 'George R.R. Martin', 'Epska fantazija o boju za železni prestol.', 'V deželi Westeros se sedem kraljevin bori za nadzor nad železnim prestolom. Hiša Stark, Lannister in Baratheon so vpletene v zapletene politične intrige. Medtem na severu za Zimskim zidom prežijo starodavne nevarnosti. Začetek epske sage Pesem ledu in ognja.', 'cover_69f5c1464d62c.jpg', 4),
(11, 'Jezero', 'Tadej Golob', 'Napet slovenski kriminalni roman o inšpektorju Tarasu Birsi, ki preiskuje brutalen umor ob Bohinjskem jezeru.', 'Višji kriminalistični inšpektor Taras Birsa se na silvestrski večer vrača s smučanja, ko v snežnem metežu naleti na policijsko patruljo in obglavljeno truplo mlade ženske ob Bohinjskem jezeru. Kljub praznikom prevzame primer in se s svojo ekipo poda na lov za neusmiljenim morilcem, pri čemer se sooča tudi z lastnimi težavami in dinamiko znotraj policijskih vrst. Roman je prva knjiga iz izjemno uspešne serije o inšpektorju Birsi in je doživel tudi zelo priljubljeno televizijsko ekranizacijo.', 'cover_69f5c1539adf0.jpg', 3),
(12, 'Alamut', 'Vladimir Bartol', 'Slovenski roman o perzijskemu vladarju in njegovih morilcih.', 'Roman se dogaja v trdnjavi Alamut v Perziji 11. stoletja. Hassan ibn Sabbah vzgaja mlade morilce s prepričanjem, da je vse dovoljeno. Roman je alegorija na totalitarizem in manipulacijo množic. Eno najpomembnejših del slovenske literature, prevedeno v dvajset jezikov.', 'cover_69f4f8f418133.png', 4),
(13, 'Steve Jobs', 'Walter Isaacson', 'Uradna biografija soustanovitelja Apple.', 'Temeljita biografija Steva Jobsa, ki temelji na več kot četrtisto intervjujih z Jobsom samim, njegovo družino, prijatelji in tekmeci. Iskrena in neprizanesljiva zgodba o enem najpomembnejših inovatorjev 20. stoletja, ki je spremenil računalnike, glasbo, telefone in filmsko animacijo.', 'cover_69f5c19027b10.jpg', 5),
(14, 'Kratka zgodovina časa', 'Stephen Hawking', 'Hawkingova razlaga vesolja za splošno javnost.', 'Stephen Hawking razloži kompleksne koncepte kozmologije, vključno z Velikim pokom, črnimi luknjami, svetlobnimi stožci in teorijo strun, na način dostopen vsakomur. Knjiga je ena najprodajanejših znanstvenih knjig vseh časov in je spremenila pogled na vesolje.', 'cover_69f5c15d76510.jpg', 6),
(15, 'Antigona', 'Sofokles', 'Klasična grška tragedija o dolžnosti in uporu.', 'Antigona se odloči pokopati brata Polinejka, kljub prepovedi kralja Kreona. Njena odločitev sproži tragičen spopad med človeško in božansko zakonodajo, med državo in posameznikovo vestjo. Ena najpomembnejših del svetovne dramske literature.', 'cover_69f5c268503b3.jpg', 7);

-- --------------------------------------------------------

--
-- Struktura tabele `BookCategory`
--

CREATE TABLE `BookCategory` (
  `BookCategoryID` int NOT NULL,
  `Title` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Odloži podatke za tabelo `BookCategory`
--

INSERT INTO `BookCategory` (`BookCategoryID`, `Title`) VALUES
(1, 'Roman'),
(2, 'Otroške knjige'),
(3, 'Kriminalke'),
(4, 'Fantazija'),
(5, 'Biografije'),
(6, 'Učbeniki'),
(7, 'Poezija'),
(8, 'Triler');

-- --------------------------------------------------------

--
-- Struktura tabele `OrderItem`
--

CREATE TABLE `OrderItem` (
  `ItemID` int NOT NULL,
  `OrderID` int NOT NULL,
  `BookID` int NOT NULL,
  `Qty` int DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Odloži podatke za tabelo `OrderItem`
--

INSERT INTO `OrderItem` (`ItemID`, `OrderID`, `BookID`, `Qty`) VALUES
(9, 6, 9, 1);

-- --------------------------------------------------------

--
-- Struktura tabele `Orders`
--

CREATE TABLE `Orders` (
  `OrderID` int NOT NULL,
  `CustomerName` varchar(100) NOT NULL,
  `CustomerEmail` varchar(100) NOT NULL,
  `OrderDate` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Odloži podatke za tabelo `Orders`
--

INSERT INTO `Orders` (`OrderID`, `CustomerName`, `CustomerEmail`, `OrderDate`) VALUES
(6, 'Aleks Novak', 'novakluka@gmail.com', '2026-05-03 12:30:22');

--
-- Indeksi zavrženih tabel
--

--
-- Indeksi tabele `Book`
--
ALTER TABLE `Book`
  ADD PRIMARY KEY (`BookID`),
  ADD KEY `BookCategoryID` (`BookCategoryID`);

--
-- Indeksi tabele `BookCategory`
--
ALTER TABLE `BookCategory`
  ADD PRIMARY KEY (`BookCategoryID`);

--
-- Indeksi tabele `OrderItem`
--
ALTER TABLE `OrderItem`
  ADD PRIMARY KEY (`ItemID`),
  ADD KEY `OrderID` (`OrderID`),
  ADD KEY `BookID` (`BookID`);

--
-- Indeksi tabele `Orders`
--
ALTER TABLE `Orders`
  ADD PRIMARY KEY (`OrderID`);

--
-- AUTO_INCREMENT zavrženih tabel
--

--
-- AUTO_INCREMENT tabele `Book`
--
ALTER TABLE `Book`
  MODIFY `BookID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT tabele `BookCategory`
--
ALTER TABLE `BookCategory`
  MODIFY `BookCategoryID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT tabele `OrderItem`
--
ALTER TABLE `OrderItem`
  MODIFY `ItemID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT tabele `Orders`
--
ALTER TABLE `Orders`
  MODIFY `OrderID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Omejitve tabel za povzetek stanja
--

--
-- Omejitve za tabelo `Book`
--
ALTER TABLE `Book`
  ADD CONSTRAINT `book_ibfk_1` FOREIGN KEY (`BookCategoryID`) REFERENCES `BookCategory` (`BookCategoryID`) ON DELETE SET NULL;

--
-- Omejitve za tabelo `OrderItem`
--
ALTER TABLE `OrderItem`
  ADD CONSTRAINT `orderitem_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `Orders` (`OrderID`) ON DELETE CASCADE,
  ADD CONSTRAINT `orderitem_ibfk_2` FOREIGN KEY (`BookID`) REFERENCES `Book` (`BookID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
