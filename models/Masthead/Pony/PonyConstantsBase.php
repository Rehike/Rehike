<?php
namespace Rehike\Model\Masthead\Pony;

/**
 * Stores pony configurations.
 * 
 * This is a base class to separate it from the Pony object class.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
abstract class PonyConstantsBase
{
    // placeholder value
    #[PonyName("<unknown pony>")]
    #[PonyColor(255, 255, 255)]
    public const UNKNOWN_PONY = 0;
    
    #[PonyName("Twilight Sparkle", PonyName::FULL_NAME)]
    #[PonyName("Twilight", PonyName::SHORT_NAME)]
    #[PonyColor(182, 137, 200)]
    public const TWILIGHT_SPARKLE = 1;
    
    #[PonyName("Pinkie Pie")]
    #[PonyColor(243, 182, 207)]
    public const PINKIE_PIE = 2;
    
    #[PonyName("Applejack")]
    #[PonyColor(255, 194, 97)]
    public const APPLEJACK = 3;
    
    #[PonyName("Rarity")]
    #[PonyColor(235, 239, 241)]
    public const RARITY = 4;
    
    #[PonyName("Rainbow Dash")]
    #[PonyColor(158, 219, 249)]
    public const RAINBOW_DASH = 5;
    
    #[PonyName("Fluttershy")]
    #[PonyColor(253, 246, 175)]
    public const FLUTTERSHY = 6;
    
    #[PonyName("Derpy Hooves", PonyName::FULL_NAME)]
    #[PonyName("Derpy", PonyName::SHORT_NAME)]
    #[PonyColor(194, 199, 214)]
    public const DERPY = 7;
    
    #[PonyName("Princess Celestia", PonyName::FULL_NAME)]
    #[PonyName("Celestia", PonyName::SHORT_NAME)]
    #[PonyColor(254, 249, 253)]
    public const CELESTIA = 8;
    
    #[PonyName("Princess Luna", PonyName::FULL_NAME)]
    #[PonyName("Luna", PonyName::SHORT_NAME)]
    #[PonyColor(101, 108, 185)]
    public const LUNA = 9;
    
    #[PonyName("Princess Cadance", PonyName::FULL_NAME)]
    #[PonyName("Cadance", PonyName::SHORT_NAME)]
    #[PonyColor(244, 203, 219)]
    public const CADANCE = 10;
    
    #[PonyName("Shining Armor")]
    #[PonyColor(255, 255, 255)]
    public const SHINING_ARMOR = 11;
    
    #[PonyName("Scootaloo")]
    #[PonyColor(251, 186, 100)]
    public const SCOOTALOO = 12;
    
    #[PonyName("Apple Bloom")]
    #[PonyColor(244, 244, 155)]
    public const APPLEBLOOM = 13;
    
    #[PonyName("Sweetie Belle")]
    #[PonyColor(239, 237, 238)]
    public const SWEETIE_BELLE = 14;
    
    #[PonyName("Big McIntosh")]
    #[PonyColor(234, 75, 91)]
    public const BIG_MCINTOSH = 15;
    
    #[PonyName("Babs Seed")]
    #[PonyColor(219, 149, 54)]
    public const BABS_SEED = 16;
    
    #[PonyName("Discord")]
    #[PonyColor(172, 167, 149)]
    public const DISCORD = 17;
    
    #[PonyName("King Sombra")]
    #[PonyColor(94, 93, 94)]
    public const SOMBRA = 18;
    
    #[PonyName("Queen Chrysalis")]
    #[PonyColor(59, 54, 56)]
    public const CHRYSALIS = 19;
    
    #[PonyName("Nightmare Moon")]
    #[PonyColor(7, 11, 15)]
    public const NIGHTMARE_MOON = 20;
    
    #[PonyName("Spike")]
    #[PonyColor(197, 144, 201)]
    public const SPIKE = 21;
    
    #[PonyName("Trixie Lulamoon", PonyName::FULL_NAME)]
    #[PonyName("Trixie", PonyName::SHORT_NAME)]
    #[PonyColor(126, 191, 233)]
    public const TRIXIE = 22;
    
    #[PonyName("Lyra Heartstrings")]
    #[PonyName("Lyra", PonyName::SHORT_NAME)]
    #[PonyColor(147, 255, 219)]
    public const LYRA = 23;
    
    #[PonyName("Bon Bon", PonyName::FULL_NAME)]
    #[PonyName("Sweetie Drops", PonyName::VARIANT_NAME)]
    #[PonyColor(245, 247, 217)]
    public const BONBON = 24;

    #[PonyName("Vinyl Scratch", PonyName::FULL_NAME)]
    #[PonyName("DJ Pon-3", PonyName::VARIANT_NAME)]
    #[PonyColor(254, 253, 231)]
    public const VINYL_SCRATCH = 25;
    
    #[PonyName("Octavia Melody", PonyName::FULL_NAME)]
    #[PonyName("Octavia", PonyName::SHORT_NAME)]
    #[PonyColor(186, 184, 176)]
    public const OCTAVIA = 26;
    
    #[PonyName("Doctor Hooves")]
    #[PonyColor(200, 187, 149)]
    public const DR_HOOVES = 27;
    
    #[PonyName("Zecora")]
    #[PonyColor(106, 105, 119)]
    public const ZECORA = 28;
    
    #[PonyName("Dinky Doo")]
    #[PonyColor(205, 190, 227)]
    public const DINKY_DOO = 29;
    
    #[PonyName("Daring Do")]
    #[PonyColor(220, 206, 116)]
    public const DARING_DO = 30;
    
    #[PonyName("Starlight Glimmer", PonyName::FULL_NAME)]
    #[PonyName("Starlight", PonyName::SHORT_NAME)]
    #[PonyColor(237, 187, 243)]
    public const STARLIGHT = 31;
}