<?php
require_once ( AG_DIR . 'classes/REDFBrokerageAPI.php' );
// Example Usage
$appKey = 'pvi03';
$password = 'ygt002260';

$api = new REDFBrokerageAPI();

// Get JWT token
$api->getToken();

// Property Data
$propertyData = [
    "brokerId" => "1",
    "brokerPropertyUrl" => "https://sakani.sa/app/mega-project/bdr-lmdyn-at-lmnwr-at-mjtm-lmkymn",
    "advertiser" => "MinistryOfHousing",
    "type" => "Villa",
    "price" => 220000,
    "regionId" => 1,
    "cityId" => 3,
    "districtId" => 10100003107,
    "chartNumber" => 1,
    "blockNumber" => 2,
    "landNumber" => 3,
    "latitude" => 24.71,
    "longitude" => 45.50,
    "postalCode" => "111",
    "deedNumber" => 0,
    "appraisal" => true,
    "structureChecked" => true,
    "insured" => true,
    "guarantee" => true,
    "subsidizable" => true,
    "specialProject" => true,
    "advertiserId" => "AA123",
    "authorizationId" => "BB456",
    "direction" => "South",
    "area" => 190,
    "length" => 10,
    "width" => 10,
    "livingRooms" => 2,
    "bedrooms" => 5,
    "bathrooms" => 5,
    "kitchens" => 1,
    "floorsInBuilding" => 2,
    "floorNumber" => 2,
    "buildYear" => 2010,
    "isKitchenFurnished" => true,
    "isFurnished" => true,
    "hasAdditionalUnit" => true,
    "hasElevator" => true,
    "hasBasement" => true,
    "hasAirConditioning" => true,
    "parking" => "None",
    "englishDescription" => "englishDescription",
    "arabicDescription" => "arabicDescription",
    "isHidden" => false,
    "media" => [
        [
            "type" => "Image",
            "isDefault" => true,
            "source" => "https://fastly.picsum.photos/id/50/4608/3072.jpg?hmac=E6WgCk6MBOyuRjW4bypT6y-tFXyWQfC_LjIBYPUspxE"
        ]
    ],
    "status" => "Available",
    "buildingNumber" => "10",
    "condition" => "Ready",
    "developerId" => "123",
    "brokerPropertyId" => "externalID123"
];

// Create a property
$response = $api->createProperty($propertyData);
prr($response);
