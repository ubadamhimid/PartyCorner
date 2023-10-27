-- Maak een nieuwe database (indien nodig)
CREATE DATABASE IF NOT EXISTS partycorner;

-- Gebruik de aangemaakte database
USE partycorner;

-- Maak een tabel om productinformatie op te slaan
CREATE TABLE producten (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    product_naam VARCHAR(255) NOT NULL,
    omschrijving VARCHAR(10255),
    leverancier VARCHAR(100),
    aangemaakt_datum DATE,
    zichtbaarheid BOOLEAN,
    prijs DECIMAL(10, 2)
);


-- Voeg producten toe aan de tabel "producten"
INSERT INTO producten (product_naam, omschrijving, leverancier, aangemaakt_datum, zichtbaarheid, prijs)
VALUES
    ('Beweegbaar skelet', 'Realistisch beweegbaar skelet voor Halloween', 'SkeletonCo', '2023-10-25', 1, 39.99),
    ('Net handschoenen', 'Zwarte handschoenen met netstof', 'GlovesRUs', '2023-10-25', 1, 5.50),
    ('Vlinder kostuum', 'Mooi vlinderkostuum voor kinderen', 'KidsParty', '2023-10-25', 1, 24.99),
    ('Ninja kostuum kind', 'Stoer ninja kostuum voor kinderen', 'KidsParty', '2023-10-25', 1, 19.99),
    ('Pinata Aap', 'Leuke aapvormige pinata voor feesten', 'PartySupplies', '2023-10-25', 1, 12.99);
