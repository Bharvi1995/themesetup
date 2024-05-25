<?php

use App\Country;
use Illuminate\Database\Seeder;

class CountryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete all records
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Country::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $countries = [
            ['name' => 'Afghanistan','full_name' => 'Islamic Republic of Afghanistan','code' => 'AF','iso3' => 'AFG','number' => '004'],
            ['name' => 'Åland Islands','full_name' => 'Åland Islands','code' => 'AX','iso3' => 'ALA','number' => '248'],
            ['name' => 'Albania','full_name' => 'Republic of Albania','code' => 'AL','iso3' => 'ALB','number' => '008'],
            ['name' => 'Algeria','full_name' => 'People\'s Democratic Republic of Algeria','code' => 'DZ','iso3' => 'DZA','number' => '012'],
            ['name' => 'American Samoa','full_name' => 'American Samoa','code' => 'AS','iso3' => 'ASM','number' => '016'],
            ['name' => 'Andorra','full_name' => 'Principality of Andorra','code' => 'AD','iso3' => 'AND','number' => '020'],
            ['name' => 'Angola','full_name' => 'Republic of Angola','code' => 'AO','iso3' => 'AGO','number' => '024'],
            ['name' => 'Anguilla','full_name' => 'Anguilla','code' => 'AI','iso3' => 'AIA','number' => '660'],
            ['name' => 'Antarctica','full_name' => 'Antarctica (the territory South of 60 deg S]','code' => 'AQ','iso3' => 'ATA','number' => '010'],
            ['name' => 'Antigua and Barbuda','full_name' => 'Antigua and Barbuda','code' => 'AG','iso3' => 'ATG','number' => '028'],
            ['name' => 'Argentina','full_name' => 'Argentine Republic','code' => 'AR','iso3' => 'ARG','number' => '032'],
            ['name' => 'Armenia','full_name' => 'Republic of Armenia','code' => 'AM','iso3' => 'ARM','number' => '051'],
            ['name' => 'Aruba','full_name' => 'Aruba','code' => 'AW','iso3' => 'ABW','number' => '533'],
            ['name' => 'Australia','full_name' => 'Commonwealth of Australia','code' => 'AU','iso3' => 'AUS','number' => '036'],
            ['name' => 'Austria','full_name' => 'Republic of Austria','code' => 'AT','iso3' => 'AUT','number' => '040'],
            ['name' => 'Azerbaijan','full_name' => 'Republic of Azerbaijan','code' => 'AZ','iso3' => 'AZE','number' => '031'],
            ['name' => 'Bahamas','full_name' => 'Commonwealth of the Bahamas','code' => 'BS','iso3' => 'BHS','number' => '044'],
            ['name' => 'Bahrain','full_name' => 'Kingdom of Bahrain','code' => 'BH','iso3' => 'BHR','number' => '048'],
            ['name' => 'Bangladesh','full_name' => 'People\'s Republic of Bangladesh','code' => 'BD','iso3' => 'BGD','number' => '050'],
            ['name' => 'Barbados','full_name' => 'Barbados','code' => 'BB','iso3' => 'BRB','number' => '052'],
            ['name' => 'Belarus','full_name' => 'Republic of Belarus','code' => 'BY','iso3' => 'BLR','number' => '112'],
            ['name' => 'Belgium','full_name' => 'Kingdom of Belgium','code' => 'BE','iso3' => 'BEL','number' => '056'],
            ['name' => 'Belize','full_name' => 'Belize','code' => 'BZ','iso3' => 'BLZ','number' => '084'],
            ['name' => 'Benin','full_name' => 'Republic of Benin','code' => 'BJ','iso3' => 'BEN','number' => '204'],
            ['name' => 'Bermuda','full_name' => 'Bermuda','code' => 'BM','iso3' => 'BMU','number' => '060'],
            ['name' => 'Bhutan','full_name' => 'Kingdom of Bhutan','code' => 'BT','iso3' => 'BTN','number' => '064'],
            ['name' => 'Bolivia','full_name' => 'Plurinational State of Bolivia','code' => 'BO','iso3' => 'BOL','number' => '068'],
            ['name' => 'Bonaire, Sint Eustatius and Saba','full_name' => 'Bonaire, Sint Eustatius and Saba','code' => 'BQ','iso3' => 'BES','number' => '535'],
            ['name' => 'Bosnia and Herzegovina','full_name' => 'Bosnia and Herzegovina','code' => 'BA','iso3' => 'BIH','number' => '070'],
            ['name' => 'Botswana','full_name' => 'Republic of Botswana','code' => 'BW','iso3' => 'BWA','number' => '072'],
            ['name' => 'Bouvet Island (Bouvetøya]','full_name' => 'Bouvet Island (Bouvetøya]','code' => 'BV','iso3' => 'BVT','number' => '074'],
            ['name' => 'Brazil','full_name' => 'Federative Republic of Brazil','code' => 'BR','iso3' => 'BRA','number' => '076'],
            ['name' => 'British Indian Ocean Territory (Chagos Archipelago]','full_name' => 'British Indian Ocean Territory (Chagos Archipelago]','code' => 'IO','iso3' => 'IOT','number' => '086'],
            ['name' => 'British Virgin Islands','full_name' => 'British Virgin Islands','code' => 'VG','iso3' => 'VGB','number' => '092'],
            ['name' => 'Brunei Darussalam','full_name' => 'Brunei Darussalam','code' => 'BN','iso3' => 'BRN','number' => '096'],
            ['name' => 'Bulgaria','full_name' => 'Republic of Bulgaria','code' => 'BG','iso3' => 'BGR','number' => '100'],
            ['name' => 'Burkina Faso','full_name' => 'Burkina Faso','code' => 'BF','iso3' => 'BFA','number' => '854'],
            ['name' => 'Burundi','full_name' => 'Republic of Burundi','code' => 'BI','iso3' => 'BDI','number' => '108'],
            ['name' => 'Cambodia','full_name' => 'Kingdom of Cambodia','code' => 'KH','iso3' => 'KHM','number' => '116'],
            ['name' => 'Cameroon','full_name' => 'Republic of Cameroon','code' => 'CM','iso3' => 'CMR','number' => '120'],
            ['name' => 'Canada','full_name' => 'Canada','code' => 'CA','iso3' => 'CAN','number' => '124'],
            ['name' => 'Cabo Verde','full_name' => 'Republic of Cabo Verde','code' => 'CV','iso3' => 'CPV','number' => '132'],
            ['name' => 'Cayman Islands','full_name' => 'Cayman Islands','code' => 'KY','iso3' => 'CYM','number' => '136'],
            ['name' => 'Central African Republic','full_name' => 'Central African Republic','code' => 'CF','iso3' => 'CAF','number' => '140'],
            ['name' => 'Chad','full_name' => 'Republic of Chad','code' => 'TD','iso3' => 'TCD','number' => '148'],
            ['name' => 'Chile','full_name' => 'Republic of Chile','code' => 'CL','iso3' => 'CHL','number' => '152'],
            ['name' => 'China','full_name' => 'People\'s Republic of China','code' => 'CN','iso3' => 'CHN','number' => '156'],
            ['name' => 'Christmas Island','full_name' => 'Christmas Island','code' => 'CX','iso3' => 'CXR','number' => '162'],
            ['name' => 'Cocos (Keeling] Islands','full_name' => 'Cocos (Keeling] Islands','code' => 'CC','iso3' => 'CCK','number' => '166'],
            ['name' => 'Colombia','full_name' => 'Republic of Colombia','code' => 'CO','iso3' => 'COL','number' => '170'],
            ['name' => 'Comoros','full_name' => 'Union of the Comoros','code' => 'KM','iso3' => 'COM','number' => '174'],
            ['name' => 'Congo','full_name' => 'Democratic Republic of the Congo','code' => 'CD','iso3' => 'COD','number' => '180'],
            ['name' => 'Congo','full_name' => 'Republic of the Congo','code' => 'CG','iso3' => 'COG','number' => '178'],
            ['name' => 'Cook Islands','full_name' => 'Cook Islands','code' => 'CK','iso3' => 'COK','number' => '184'],
            ['name' => 'Costa Rica','full_name' => 'Republic of Costa Rica','code' => 'CR','iso3' => 'CRI','number' => '188'],
            ['name' => 'Cote d\'Ivoire','full_name' => 'Republic of Cote d\'Ivoire','code' => 'CI','iso3' => 'CIV','number' => '384'],
            ['name' => 'Croatia','full_name' => 'Republic of Croatia','code' => 'HR','iso3' => 'HRV','number' => '191'],
            ['name' => 'Cuba','full_name' => 'Republic of Cuba','code' => 'CU','iso3' => 'CUB','number' => '192'],
            ['name' => 'Curaçao','full_name' => 'Curaçao','code' => 'CW','iso3' => 'CUW','number' => '531'],
            ['name' => 'Cyprus','full_name' => 'Republic of Cyprus','code' => 'CY','iso3' => 'CYP','number' => '196'],
            ['name' => 'Czechia','full_name' => 'Czech Republic','code' => 'CZ','iso3' => 'CZE','number' => '203'],
            ['name' => 'Denmark','full_name' => 'Kingdom of Denmark','code' => 'DK','iso3' => 'DNK','number' => '208'],
            ['name' => 'Djibouti','full_name' => 'Republic of Djibouti','code' => 'DJ','iso3' => 'DJI','number' => '262'],
            ['name' => 'Dominica','full_name' => 'Commonwealth of Dominica','code' => 'DM','iso3' => 'DMA','number' => '212'],
            ['name' => 'Dominican Republic','full_name' => 'Dominican Republic','code' => 'DO','iso3' => 'DOM','number' => '214'],
            ['name' => 'Ecuador','full_name' => 'Republic of Ecuador','code' => 'EC','iso3' => 'ECU','number' => '218'],
            ['name' => 'Egypt','full_name' => 'Arab Republic of Egypt','code' => 'EG','iso3' => 'EGY','number' => '818'],
            ['name' => 'El Salvador','full_name' => 'Republic of El Salvador','code' => 'SV','iso3' => 'SLV','number' => '222'],
            ['name' => 'Equatorial Guinea','full_name' => 'Republic of Equatorial Guinea','code' => 'GQ','iso3' => 'GNQ','number' => '226'],
            ['name' => 'Eritrea','full_name' => 'State of Eritrea','code' => 'ER','iso3' => 'ERI','number' => '232'],
            ['name' => 'Estonia','full_name' => 'Republic of Estonia','code' => 'EE','iso3' => 'EST','number' => '233'],
            ['name' => 'Ethiopia','full_name' => 'Federal Democratic Republic of Ethiopia','code' => 'ET','iso3' => 'ETH','number' => '231'],
            ['name' => 'Faroe Islands','full_name' => 'Faroe Islands','code' => 'FO','iso3' => 'FRO','number' => '234'],
            ['name' => 'Falkland Islands (Malvinas]','full_name' => 'Falkland Islands (Malvinas]','code' => 'FK','iso3' => 'FLK','number' => '238'],
            ['name' => 'Fiji','full_name' => 'Republic of Fiji','code' => 'FJ','iso3' => 'FJI','number' => '242'],
            ['name' => 'Finland','full_name' => 'Republic of Finland','code' => 'FI','iso3' => 'FIN','number' => '246'],
            ['name' => 'France','full_name' => 'French Republic','code' => 'FR','iso3' => 'FRA','number' => '250'],
            ['name' => 'French Guiana','full_name' => 'French Guiana','code' => 'GF','iso3' => 'GUF','number' => '254'],
            ['name' => 'French Polynesia','full_name' => 'French Polynesia','code' => 'PF','iso3' => 'PYF','number' => '258'],
            ['name' => 'French Southern Territories','full_name' => 'French Southern Territories','code' => 'TF','iso3' => 'ATF','number' => '260'],
            ['name' => 'Gabon','full_name' => 'Gabonese Republic','code' => 'GA','iso3' => 'GAB','number' => '266'],
            ['name' => 'Gambia','full_name' => 'Republic of the Gambia','code' => 'GM','iso3' => 'GMB','number' => '270'],
            ['name' => 'Georgia','full_name' => 'Georgia','code' => 'GE','iso3' => 'GEO','number' => '268'],
            ['name' => 'Germany','full_name' => 'Federal Republic of Germany','code' => 'DE','iso3' => 'DEU','number' => '276'],
            ['name' => 'Ghana','full_name' => 'Republic of Ghana','code' => 'GH','iso3' => 'GHA','number' => '288'],
            ['name' => 'Gibraltar','full_name' => 'Gibraltar','code' => 'GI','iso3' => 'GIB','number' => '292'],
            ['name' => 'Greece','full_name' => 'Hellenic Republic of Greece','code' => 'GR','iso3' => 'GRC','number' => '300'],
            ['name' => 'Greenland','full_name' => 'Greenland','code' => 'GL','iso3' => 'GRL','number' => '304'],
            ['name' => 'Grenada','full_name' => 'Grenada','code' => 'GD','iso3' => 'GRD','number' => '308'],
            ['name' => 'Guadeloupe','full_name' => 'Guadeloupe','code' => 'GP','iso3' => 'GLP','number' => '312'],
            ['name' => 'Guam','full_name' => 'Guam','code' => 'GU','iso3' => 'GUM','number' => '316'],
            ['name' => 'Guatemala','full_name' => 'Republic of Guatemala','code' => 'GT','iso3' => 'GTM','number' => '320'],
            ['name' => 'Guernsey','full_name' => 'Bailiwick of Guernsey','code' => 'GG','iso3' => 'GGY','number' => '831'],
            ['name' => 'Guinea','full_name' => 'Republic of Guinea','code' => 'GN','iso3' => 'GIN','number' => '324'],
            ['name' => 'Guinea-Bissau','full_name' => 'Republic of Guinea-Bissau','code' => 'GW','iso3' => 'GNB','number' => '624'],
            ['name' => 'Guyana','full_name' => 'Co-operative Republic of Guyana','code' => 'GY','iso3' => 'GUY','number' => '328'],
            ['name' => 'Haiti','full_name' => 'Republic of Haiti','code' => 'HT','iso3' => 'HTI','number' => '332'],
            ['name' => 'Heard Island and McDonald Islands','full_name' => 'Heard Island and McDonald Islands','code' => 'HM','iso3' => 'HMD','number' => '334'],
            ['name' => 'Holy See (Vatican City State]','full_name' => 'Holy See (Vatican City State]','code' => 'VA','iso3' => 'VAT','number' => '336'],
            ['name' => 'Honduras','full_name' => 'Republic of Honduras','code' => 'HN','iso3' => 'HND','number' => '340'],
            ['name' => 'Hong Kong','full_name' => 'Hong Kong Special Administrative Region of China','code' => 'HK','iso3' => 'HKG','number' => '344'],
            ['name' => 'Hungary','full_name' => 'Hungary','code' => 'HU','iso3' => 'HUN','number' => '348'],
            ['name' => 'Iceland','full_name' => 'Republic of Iceland','code' => 'IS','iso3' => 'ISL','number' => '352'],
            ['name' => 'India','full_name' => 'Republic of India','code' => 'IN','iso3' => 'IND','number' => '356'],
            ['name' => 'Indonesia','full_name' => 'Republic of Indonesia','code' => 'ID','iso3' => 'IDN','number' => '360'],
            ['name' => 'Iran','full_name' => 'Islamic Republic of Iran','code' => 'IR','iso3' => 'IRN','number' => '364'],
            ['name' => 'Iraq','full_name' => 'Republic of Iraq','code' => 'IQ','iso3' => 'IRQ','number' => '368'],
            ['name' => 'Ireland','full_name' => 'Ireland','code' => 'IE','iso3' => 'IRL','number' => '372'],
            ['name' => 'Isle of Man','full_name' => 'Isle of Man','code' => 'IM','iso3' => 'IMN','number' => '833'],
            ['name' => 'Israel','full_name' => 'State of Israel','code' => 'IL','iso3' => 'ISR','number' => '376'],
            ['name' => 'Italy','full_name' => 'Republic of Italy','code' => 'IT','iso3' => 'ITA','number' => '380'],
            ['name' => 'Jamaica','full_name' => 'Jamaica','code' => 'JM','iso3' => 'JAM','number' => '388'],
            ['name' => 'Japan','full_name' => 'Japan','code' => 'JP','iso3' => 'JPN','number' => '392'],
            ['name' => 'Jersey','full_name' => 'Bailiwick of Jersey','code' => 'JE','iso3' => 'JEY','number' => '832'],
            ['name' => 'Jordan','full_name' => 'Hashemite Kingdom of Jordan','code' => 'JO','iso3' => 'JOR','number' => '400'],
            ['name' => 'Kazakhstan','full_name' => 'Republic of Kazakhstan','code' => 'KZ','iso3' => 'KAZ','number' => '398'],
            ['name' => 'Kenya','full_name' => 'Republic of Kenya','code' => 'KE','iso3' => 'KEN','number' => '404'],
            ['name' => 'Kiribati','full_name' => 'Republic of Kiribati','code' => 'KI','iso3' => 'KIR','number' => '296'],
            ['name' => 'Korea','full_name' => 'Democratic People\'s Republic of Korea','code' => 'KP','iso3' => 'PRK','number' => '408'],
            ['name' => 'Korea','full_name' => 'Republic of Korea','code' => 'KR','iso3' => 'KOR','number' => '410'],
            ['name' => 'Kuwait','full_name' => 'State of Kuwait','code' => 'KW','iso3' => 'KWT','number' => '414'],
            ['name' => 'Kyrgyz Republic','full_name' => 'Kyrgyz Republic','code' => 'KG','iso3' => 'KGZ','number' => '417'],
            ['name' => 'Lao People\'s Democratic Republic','full_name' => 'Lao People\'s Democratic Republic','code' => 'LA','iso3' => 'LAO','number' => '418'],
            ['name' => 'Latvia','full_name' => 'Republic of Latvia','code' => 'LV','iso3' => 'LVA','number' => '428'],
            ['name' => 'Lebanon','full_name' => 'Lebanese Republic','code' => 'LB','iso3' => 'LBN','number' => '422'],
            ['name' => 'Lesotho','full_name' => 'Kingdom of Lesotho','code' => 'LS','iso3' => 'LSO','number' => '426'],
            ['name' => 'Liberia','full_name' => 'Republic of Liberia','code' => 'LR','iso3' => 'LBR','number' => '430'],
            ['name' => 'Libya','full_name' => 'State of Libya','code' => 'LY','iso3' => 'LBY','number' => '434'],
            ['name' => 'Liechtenstein','full_name' => 'Principality of Liechtenstein','code' => 'LI','iso3' => 'LIE','number' => '438'],
            ['name' => 'Lithuania','full_name' => 'Republic of Lithuania','code' => 'LT','iso3' => 'LTU','number' => '440'],
            ['name' => 'Luxembourg','full_name' => 'Grand Duchy of Luxembourg','code' => 'LU','iso3' => 'LUX','number' => '442'],
            ['name' => 'Macao','full_name' => 'Macao Special Administrative Region of China','code' => 'MO','iso3' => 'MAC','number' => '446'],
            ['name' => 'Madagascar','full_name' => 'Republic of Madagascar','code' => 'MG','iso3' => 'MDG','number' => '450'],
            ['name' => 'Malawi','full_name' => 'Republic of Malawi','code' => 'MW','iso3' => 'MWI','number' => '454'],
            ['name' => 'Malaysia','full_name' => 'Malaysia','code' => 'MY','iso3' => 'MYS','number' => '458'],
            ['name' => 'Maldives','full_name' => 'Republic of Maldives','code' => 'MV','iso3' => 'MDV','number' => '462'],
            ['name' => 'Mali','full_name' => 'Republic of Mali','code' => 'ML','iso3' => 'MLI','number' => '466'],
            ['name' => 'Malta','full_name' => 'Republic of Malta','code' => 'MT','iso3' => 'MLT','number' => '470'],
            ['name' => 'Marshall Islands','full_name' => 'Republic of the Marshall Islands','code' => 'MH','iso3' => 'MHL','number' => '584'],
            ['name' => 'Martinique','full_name' => 'Martinique','code' => 'MQ','iso3' => 'MTQ','number' => '474'],
            ['name' => 'Mauritania','full_name' => 'Islamic Republic of Mauritania','code' => 'MR','iso3' => 'MRT','number' => '478'],
            ['name' => 'Mauritius','full_name' => 'Republic of Mauritius','code' => 'MU','iso3' => 'MUS','number' => '480'],
            ['name' => 'Mayotte','full_name' => 'Mayotte','code' => 'YT','iso3' => 'MYT','number' => '175'],
            ['name' => 'Mexico','full_name' => 'United Mexican States','code' => 'MX','iso3' => 'MEX','number' => '484'],
            ['name' => 'Micronesia','full_name' => 'Federated States of Micronesia','code' => 'FM','iso3' => 'FSM','number' => '583'],
            ['name' => 'Moldova','full_name' => 'Republic of Moldova','code' => 'MD','iso3' => 'MDA','number' => '498'],
            ['name' => 'Monaco','full_name' => 'Principality of Monaco','code' => 'MC','iso3' => 'MCO','number' => '492'],
            ['name' => 'Mongolia','full_name' => 'Mongolia','code' => 'MN','iso3' => 'MNG','number' => '496'],
            ['name' => 'Montenegro','full_name' => 'Montenegro','code' => 'ME','iso3' => 'MNE','number' => '499'],
            ['name' => 'Montserrat','full_name' => 'Montserrat','code' => 'MS','iso3' => 'MSR','number' => '500'],
            ['name' => 'Morocco','full_name' => 'Kingdom of Morocco','code' => 'MA','iso3' => 'MAR','number' => '504'],
            ['name' => 'Mozambique','full_name' => 'Republic of Mozambique','code' => 'MZ','iso3' => 'MOZ','number' => '508'],
            ['name' => 'Myanmar','full_name' => 'Republic of the Union of Myanmar','code' => 'MM','iso3' => 'MMR','number' => '104'],
            ['name' => 'Namibia','full_name' => 'Republic of Namibia','code' => 'NA','iso3' => 'NAM','number' => '516'],
            ['name' => 'Nauru','full_name' => 'Republic of Nauru','code' => 'NR','iso3' => 'NRU','number' => '520'],
            ['name' => 'Nepal','full_name' => 'Nepal','code' => 'NP','iso3' => 'NPL','number' => '524'],
            ['name' => 'Netherlands','full_name' => 'Kingdom of the Netherlands','code' => 'NL','iso3' => 'NLD','number' => '528'],
            ['name' => 'New Caledonia','full_name' => 'New Caledonia','code' => 'NC','iso3' => 'NCL','number' => '540'],
            ['name' => 'New Zealand','full_name' => 'New Zealand','code' => 'NZ','iso3' => 'NZL','number' => '554'],
            ['name' => 'Nicaragua','full_name' => 'Republic of Nicaragua','code' => 'NI','iso3' => 'NIC','number' => '558'],
            ['name' => 'Niger','full_name' => 'Republic of Niger','code' => 'NE','iso3' => 'NER','number' => '562'],
            ['name' => 'Nigeria','full_name' => 'Federal Republic of Nigeria','code' => 'NG','iso3' => 'NGA','number' => '566'],
            ['name' => 'Niue','full_name' => 'Niue','code' => 'NU','iso3' => 'NIU','number' => '570'],
            ['name' => 'Norfolk Island','full_name' => 'Norfolk Island','code' => 'NF','iso3' => 'NFK','number' => '574'],
            ['name' => 'North Macedonia','full_name' => 'Republic of North Macedonia','code' => 'MK','iso3' => 'MKD','number' => '807'],
            ['name' => 'Northern Mariana Islands','full_name' => 'Commonwealth of the Northern Mariana Islands','code' => 'MP','iso3' => 'MNP','number' => '580'],
            ['name' => 'Norway','full_name' => 'Kingdom of Norway','code' => 'NO','iso3' => 'NOR','number' => '578'],
            ['name' => 'Oman','full_name' => 'Sultanate of Oman','code' => 'OM','iso3' => 'OMN','number' => '512'],
            ['name' => 'Pakistan','full_name' => 'Islamic Republic of Pakistan','code' => 'PK','iso3' => 'PAK','number' => '586'],
            ['name' => 'Palau','full_name' => 'Republic of Palau','code' => 'PW','iso3' => 'PLW','number' => '585'],
            ['name' => 'Palestine','full_name' => 'State of Palestine','code' => 'PS','iso3' => 'PSE','number' => '275'],
            ['name' => 'Panama','full_name' => 'Republic of Panama','code' => 'PA','iso3' => 'PAN','number' => '591'],
            ['name' => 'Papua New Guinea','full_name' => 'Independent State of Papua New Guinea','code' => 'PG','iso3' => 'PNG','number' => '598'],
            ['name' => 'Paraguay','full_name' => 'Republic of Paraguay','code' => 'PY','iso3' => 'PRY','number' => '600'],
            ['name' => 'Peru','full_name' => 'Republic of Peru','code' => 'PE','iso3' => 'PER','number' => '604'],
            ['name' => 'Philippines','full_name' => 'Republic of the Philippines','code' => 'PH','iso3' => 'PHL','number' => '608'],
            ['name' => 'Pitcairn Islands','full_name' => 'Pitcairn Islands','code' => 'PN','iso3' => 'PCN','number' => '612'],
            ['name' => 'Poland','full_name' => 'Republic of Poland','code' => 'PL','iso3' => 'POL','number' => '616'],
            ['name' => 'Portugal','full_name' => 'Portuguese Republic','code' => 'PT','iso3' => 'PRT','number' => '620'],
            ['name' => 'Puerto Rico','full_name' => 'Commonwealth of Puerto Rico','code' => 'PR','iso3' => 'PRI','number' => '630'],
            ['name' => 'Qatar','full_name' => 'State of Qatar','code' => 'QA','iso3' => 'QAT','number' => '634'],
            ['name' => 'Réunion','full_name' => 'Réunion','code' => 'RE','iso3' => 'REU','number' => '638'],
            ['name' => 'Romania','full_name' => 'Romania','code' => 'RO','iso3' => 'ROU','number' => '642'],
            ['name' => 'Russian Federation','full_name' => 'Russian Federation','code' => 'RU','iso3' => 'RUS','number' => '643'],
            ['name' => 'Rwanda','full_name' => 'Republic of Rwanda','code' => 'RW','iso3' => 'RWA','number' => '646'],
            ['name' => 'Saint Barthélemy','full_name' => 'Saint Barthélemy','code' => 'BL','iso3' => 'BLM','number' => '652'],
            ['name' => 'Saint Helena, Ascension and Tristan da Cunha','full_name' => 'Saint Helena, Ascension and Tristan da Cunha','code' => 'SH','iso3' => 'SHN','number' => '654'],
            ['name' => 'Saint Kitts and Nevis','full_name' => 'Federation of Saint Kitts and Nevis','code' => 'KN','iso3' => 'KNA','number' => '659'],
            ['name' => 'Saint Lucia','full_name' => 'Saint Lucia','code' => 'LC','iso3' => 'LCA','number' => '662'],
            ['name' => 'Saint Martin','full_name' => 'Saint Martin (French part]','code' => 'MF','iso3' => 'MAF','number' => '663'],
            ['name' => 'Saint Pierre and Miquelon','full_name' => 'Saint Pierre and Miquelon','code' => 'PM','iso3' => 'SPM','number' => '666'],
            ['name' => 'Saint Vincent and the Grenadines','full_name' => 'Saint Vincent and the Grenadines','code' => 'VC','iso3' => 'VCT','number' => '670'],
            ['name' => 'Samoa','full_name' => 'Independent State of Samoa','code' => 'WS','iso3' => 'WSM','number' => '882'],
            ['name' => 'San Marino','full_name' => 'Republic of San Marino','code' => 'SM','iso3' => 'SMR','number' => '674'],
            ['name' => 'Sao Tome and Principe','full_name' => 'Democratic Republic of Sao Tome and Principe','code' => 'ST','iso3' => 'STP','number' => '678'],
            ['name' => 'Saudi Arabia','full_name' => 'Kingdom of Saudi Arabia','code' => 'SA','iso3' => 'SAU','number' => '682'],
            ['name' => 'Senegal','full_name' => 'Republic of Senegal','code' => 'SN','iso3' => 'SEN','number' => '686'],
            ['name' => 'Serbia','full_name' => 'Republic of Serbia','code' => 'RS','iso3' => 'SRB','number' => '688'],
            ['name' => 'Seychelles','full_name' => 'Republic of Seychelles','code' => 'SC','iso3' => 'SYC','number' => '690'],
            ['name' => 'Sierra Leone','full_name' => 'Republic of Sierra Leone','code' => 'SL','iso3' => 'SLE','number' => '694'],
            ['name' => 'Singapore','full_name' => 'Republic of Singapore','code' => 'SG','iso3' => 'SGP','number' => '702'],
            ['name' => 'Sint Maarten (Dutch part]','full_name' => 'Sint Maarten (Dutch part]','code' => 'SX','iso3' => 'SXM','number' => '534'],
            ['name' => 'Slovakia (Slovak Republic]','full_name' => 'Slovakia (Slovak Republic]','code' => 'SK','iso3' => 'SVK','number' => '703'],
            ['name' => 'Slovenia','full_name' => 'Republic of Slovenia','code' => 'SI','iso3' => 'SVN','number' => '705'],
            ['name' => 'Solomon Islands','full_name' => 'Solomon Islands','code' => 'SB','iso3' => 'SLB','number' => '090'],
            ['name' => 'Somalia','full_name' => 'Federal Republic of Somalia','code' => 'SO','iso3' => 'SOM','number' => '706'],
            ['name' => 'South Africa','full_name' => 'Republic of South Africa','code' => 'ZA','iso3' => 'ZAF','number' => '710'],
            ['name' => 'South Georgia and the South Sandwich Islands','full_name' => 'South Georgia and the South Sandwich Islands','code' => 'GS','iso3' => 'SGS','number' => '239'],
            ['name' => 'South Sudan','full_name' => 'Republic of South Sudan','code' => 'SS','iso3' => 'SSD','number' => '728'],
            ['name' => 'Spain','full_name' => 'Kingdom of Spain','code' => 'ES','iso3' => 'ESP','number' => '724'],
            ['name' => 'Sri Lanka','full_name' => 'Democratic Socialist Republic of Sri Lanka','code' => 'LK','iso3' => 'LKA','number' => '144'],
            ['name' => 'Sudan','full_name' => 'Republic of Sudan','code' => 'SD','iso3' => 'SDN','number' => '729'],
            ['name' => 'Suriname','full_name' => 'Republic of Suriname','code' => 'SR','iso3' => 'SUR','number' => '740'],
            ['name' => 'Svalbard & Jan Mayen Islands','full_name' => 'Svalbard & Jan Mayen Islands','code' => 'SJ','iso3' => 'SJM','number' => '744'],
            ['name' => 'Eswatini','full_name' => 'Kingdom of Eswatini','code' => 'SZ','iso3' => 'SWZ','number' => '748'],
            ['name' => 'Sweden','full_name' => 'Kingdom of Sweden','code' => 'SE','iso3' => 'SWE','number' => '752'],
            ['name' => 'Switzerland','full_name' => 'Swiss Confederation','code' => 'CH','iso3' => 'CHE','number' => '756'],
            ['name' => 'Syrian Arab Republic','full_name' => 'Syrian Arab Republic','code' => 'SY','iso3' => 'SYR','number' => '760'],
            ['name' => 'Taiwan','full_name' => 'Taiwan, Province of China','code' => 'TW','iso3' => 'TWN','number' => '158'],
            ['name' => 'Tajikistan','full_name' => 'Republic of Tajikistan','code' => 'TJ','iso3' => 'TJK','number' => '762'],
            ['name' => 'Tanzania','full_name' => 'United Republic of Tanzania','code' => 'TZ','iso3' => 'TZA','number' => '834'],
            ['name' => 'Thailand','full_name' => 'Kingdom of Thailand','code' => 'TH','iso3' => 'THA','number' => '764'],
            ['name' => 'Timor-Leste','full_name' => 'Democratic Republic of Timor-Leste','code' => 'TL','iso3' => 'TLS','number' => '626'],
            ['name' => 'Togo','full_name' => 'Togolese Republic','code' => 'TG','iso3' => 'TGO','number' => '768'],
            ['name' => 'Tokelau','full_name' => 'Tokelau','code' => 'TK','iso3' => 'TKL','number' => '772'],
            ['name' => 'Tonga','full_name' => 'Kingdom of Tonga','code' => 'TO','iso3' => 'TON','number' => '776'],
            ['name' => 'Trinidad and Tobago','full_name' => 'Republic of Trinidad and Tobago','code' => 'TT','iso3' => 'TTO','number' => '780'],
            ['name' => 'Tunisia','full_name' => 'Tunisian Republic','code' => 'TN','iso3' => 'TUN','number' => '788'],
            ['name' => 'Turkey','full_name' => 'Republic of Turkey','code' => 'TR','iso3' => 'TUR','number' => '792'],
            ['name' => 'Turkmenistan','full_name' => 'Turkmenistan','code' => 'TM','iso3' => 'TKM','number' => '795'],
            ['name' => 'Turks and Caicos Islands','full_name' => 'Turks and Caicos Islands','code' => 'TC','iso3' => 'TCA','number' => '796'],
            ['name' => 'Tuvalu','full_name' => 'Tuvalu','code' => 'TV','iso3' => 'TUV','number' => '798'],
            ['name' => 'Uganda','full_name' => 'Republic of Uganda','code' => 'UG','iso3' => 'UGA','number' => '800'],
            ['name' => 'Ukraine','full_name' => 'Ukraine','code' => 'UA','iso3' => 'UKR','number' => '804'],
            ['name' => 'United Arab Emirates','full_name' => 'United Arab Emirates','code' => 'AE','iso3' => 'ARE','number' => '784'],
            ['name' => 'United Kingdom of Great Britain and Northern Ireland','full_name' => 'United Kingdom of Great Britain & Northern Ireland','code' => 'GB','iso3' => 'GBR','number' => '826'],
            ['name' => 'United States of America','full_name' => 'United States of America','code' => 'US','iso3' => 'USA','number' => '840'],
            ['name' => 'United States Minor Outlying Islands','full_name' => 'United States Minor Outlying Islands','code' => 'UM','iso3' => 'UMI','number' => '581'],
            ['name' => 'United States Virgin Islands','full_name' => 'United States Virgin Islands','code' => 'VI','iso3' => 'VIR','number' => '850'],
            ['name' => 'Uruguay','full_name' => 'Eastern Republic of Uruguay','code' => 'UY','iso3' => 'URY','number' => '858'],
            ['name' => 'Uzbekistan','full_name' => 'Republic of Uzbekistan','code' => 'UZ','iso3' => 'UZB','number' => '860'],
            ['name' => 'Vanuatu','full_name' => 'Republic of Vanuatu','code' => 'VU','iso3' => 'VUT','number' => '548'],
            ['name' => 'Venezuela','full_name' => 'Bolivarian Republic of Venezuela','code' => 'VE','iso3' => 'VEN','number' => '862'],
            ['name' => 'Vietnam','full_name' => 'Socialist Republic of Vietnam','code' => 'VN','iso3' => 'VNM','number' => '704'],
            ['name' => 'Wallis and Futuna','full_name' => 'Wallis and Futuna','code' => 'WF','iso3' => 'WLF','number' => '876'],
            ['name' => 'Western Sahara','full_name' => 'Western Sahara','code' => 'EH','iso3' => 'ESH','number' => '732'],
            ['name' => 'Yemen','full_name' => 'Yemen','code' => 'YE','iso3' => 'YEM','number' => '887'],
            ['name' => 'Zambia','full_name' => 'Republic of Zambia','code' => 'ZM','iso3' => 'ZMB','number' => '894'],
            ['name' => 'Zimbabwe','full_name' => 'Republic of Zimbabwe','code' => 'ZW','iso3' => 'ZWE','number' => '716']
        ];

        // Add all countries to the table
        Country::insert($countries);

        $date_upd = [
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        Country::whereNull('created_at')->update($date_upd);
    }
}
