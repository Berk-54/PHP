<?php

class Huis
{
    // Eigenschappen (properties)
    private int $aantalVerdiepingen;
    private int $aantalKamers;
    private float $breedte;
    private float $hoogte;
    private float $diepte;
    private float $volume;

    // Constante voor prijs per m³
    private const PRIJS_PER_M3 = 1500;

    // Constructor
    public function __construct(int $aantalVerdiepingen, int $aantalKamers, float $breedte, float $hoogte, float $diepte)
    {
        $this->setAantalVerdiepingen($aantalVerdiepingen);
        $this->setAantalKamers($aantalKamers);
        $this->setBreedte($breedte);
        $this->setHoogte($hoogte);
        $this->setDiepte($diepte);
        $this->berekenVolume();
    }

    // Setters
    public function setAantalVerdiepingen(int $aantalVerdiepingen): void
    {
        $this->aantalVerdiepingen = $aantalVerdiepingen;
    }

    public function setAantalKamers(int $aantalKamers): void
    {
        $this->aantalKamers = $aantalKamers;
    }

    public function setBreedte(float $breedte): void
    {
        $this->breedte = $breedte;
    }

    public function setHoogte(float $hoogte): void
    {
        $this->hoogte = $hoogte;
    }

    public function setDiepte(float $diepte): void
    {
        $this->diepte = $diepte;
    }

    private function setVolume(float $volume): void
    {
        $this->volume = round($volume, 2); // Afronden op 2 decimalen
    }

    // Getters
    public function getAantalVerdiepingen(): int
    {
        return $this->aantalVerdiepingen;
    }

    public function getAantalKamers(): int
    {
        return $this->aantalKamers;
    }

    public function getBreedte(): float
    {
        return $this->breedte;
    }

    public function getHoogte(): float
    {
        return $this->hoogte;
    }

    public function getDiepte(): float
    {
        return $this->diepte;
    }

    public function getVolume(): float
    {
        return $this->volume;
    }

    // Methode om het volume te berekenen
    public function berekenVolume(): void
    {
        $volume = $this->breedte * $this->hoogte * $this->diepte;
        $this->setVolume($volume);
    }

    // Methode om de prijs te berekenen
    public function berekenPrijs(): float
    {
        return $this->volume * self::PRIJS_PER_M3;
    }

    // Methode om alles te tonen
    public function toonDetails(): void
    {
        echo "Details van het huis:\n";
        echo "Aantal verdiepingen: " . $this->getAantalVerdiepingen() . "\n";
        echo "Aantal kamers: " . $this->getAantalKamers() . "\n";
        echo "Breedte: " . $this->getBreedte() . " meter\n";
        echo "Hoogte: " . $this->getHoogte() . " meter\n";
        echo "Diepte: " . $this->getDiepte() . " meter\n";
        echo "Volume: " . $this->getVolume() . " m³\n";
        echo "Prijs: €" . number_format($this->berekenPrijs(), 2, ',', '.') . "\n";
        echo "---------------------------\n";
    }
}
