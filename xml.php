<?php
$fecha = new DateTime;
$xml = new SimpleXMLElement('<xml/>');
$xml->addAttribute('version', "1.0");
$xml->addAttribute('encoding', "utf-8");
$fatca_oecd = $xml->addChild('0:ftc:FATCA_OECD');
$fatca_oecd->addAttribute('0:xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
$fatca_oecd->addAttribute('0:xmlns', 'urn:oecd:ties:fatca:v1');
$fatca_oecd->addAttribute('0:xmlns:ftc', 'urn:oecd:ties:fatca:v1');
$fatca_oecd->addAttribute('0:xmlns:iso','urn:oecd:ties:isofatcatypes:v1');
$fatca_oecd->addAttribute('0:xmlns:sfa','urn:oecd:ties:stffatcatypes:v1');
$fatca_oecd->addAttribute('version','1.1');

$messageSpec = $fatca_oecd->addChild('0:ftc:MessageSpec');
$messageSpec->addChild('0:sfa:SendingCompanyIN','000000.00000.TA.124');
$messageSpec->addChild('0:sfa:TransmittingCountry', 'AR');
$messageSpec->addChild('0:sfa:ReceivingCountry', 'US');
$messageSpec->addChild('0:sfa:MessageType', 'FATCA');
$messageSpec->addChild('0:sfa:Warning');
$messageSpec->addChild('0:sfa:Contact');
$messageSpec->addChild('0:sfa:MessageRefId', '1');
$messageSpec->addChild('0:sfa:ReportingPeriod', '2014-12-31');
$messageSpec->addChild('0:sfa:TimeStamp', $fecha->format('Y-m-d\TH:i:s'));

$fatca = $fatca_oecd->addChild('0:ftc:FATCA');

$reportingFI = $fatca->addChild('0:ftc:ReportingFI');
$reportingFI->addChild('0:sfa:ResCountryCode','CA');
$TIN = $reportingFI->addChild('0:sfa:TIN','123456789');
$TIN->addAttribute('issuedBy','US');
$reportingFI->addChild('0:sfa:Name','Allaria Ledesma');
$address = $reportingFI->addChild('0:sfa:Address');
$address->addChild('0:sfa:CountryCode', 'AR');
$addressFix = $address->addChild('AddressFix');
$addressFix->addChild('0:sfa:Street','25 de Mayo');
$addressFix->addChild('0:sfa:BuildingIdentifier','359');
$addressFix->addChild('0:sfa:SuiteIdentifier');
$addressFix->addChild('0:sfa:FloorIdentifier','12');
$addressFix->addChild('0:sfa:DistrictName');
$addressFix->addChild('0:sfa:POB');
$addressFix->addChild('0:sfa:PostCode','C1002ABG');
$addressFix->addChild('0:sfa:City','Ciudad de Buenos Aires');
$addressFix->addChild('0:sfa:CountrySubEntity','AR');

$accountReport = $reportingFI->addChild('0:ftc:AccountReport');
$docSpec = $accountReport->addChild('0:ftc:DocSpec');
$docSpec->addChild('0:ftc:DocTypeIndic','FATCA11');
$docSpec->addChild('0:ftc:DocRefId','Ref ID123');
$docSpec->addChild('0:ftc:CorrMessageRefId');
$docSpec->addChild('0:ftc:CorrDocRefId');

$accountReport->addChild('0:ftc:AccountNumber', 'ABCD12345');

$accountHolder = $accountReport->addChild('0:ftc:AccountHolder');

$individual = $accountHolder->addChild('0:ftc:Invividual');
$individual->addChild('0:sfa:ResCountryCode', 'CA');
$individualTIN = $individual->addChild('0:sfa:TIN', '123456789');
$individualTIN->addAttribute('issuedBy', 'US');
$individualName = $individual->addChild('0:sfa:Name');
$individualName->addChild('0:sfa:PrecedingTitle');
$individualName->addChild('0:sfa:Title');
$individualName->addChild('0:sfa:FirstName', 'John');
$individualName->addChild('0:sfa:MiddleName','Peter');
$individualName->addChild('0:sfa:NamePrefix');
$individualName->addChild('0:sfa:LastName','Smith');
$individualName->addChild('0:sfa:GenerationIdentifier');
$individualName->addChild('0:sfa:Suffix');
$individualName->addChild('0:sfa:GeneralSuffix');
$individualAddress = $individual->addChild('0:sfa:Address');
$individualAddress->addChild('0:sfa:CountryCode', 'US');
$individualAddressFix = $individualAddress->addChild('0:sfa:AddressFix');
$individualAddressFix->addChild('0:sfa:Street', '234 Street');
$individualAddressFix->addChild('0:sfa:BuildingIdentifier');
$individualAddressFix->addChild('0:sfa:SuiteIdentifier');
$individualAddressFix->addChild('0:sfa:FloorIdentifier');
$individualAddressFix->addChild('0:sfa:DistrictName');
$individualAddressFix->addChild('0:sfa:POB');
$individualAddressFix->addChild('0:sfa:PostCode', '75244');
$individualAddressFix->addChild('0:sfa:City','City');
$individualAddressFix->addChild('0:sfa:CountrySubEntity', 'US');
$individualBirthInfo = $individual->addChild('0:sfa:BirthInfo');
$individualBirthInfo->addChild('0:sfa:BirthDate', '1960-01-01');
$individualBirthInfo->addChild('0:sfa:City');
$individualBirthInfo->addChild('0:sfa:CitySubEntity');

$organisation = $accountHolder->addChild('0:ftc:Organisation');
$organisation->addChild('0:sfa:ResCountryCode', 'CA');
$organisationTIN = $organisation->addChild('0:sfa:TIN', '123456789');
$organisationTIN->addAttribute('issuedBy', 'US');
$organisationName = $organisation->addChild('0:sfa:Name', 'Organisation Name');
$organisationAddress = $individual->addChild('0:sfa:Address');
$organisationAddress->addChild('0:sfa:CountryCode', 'US');
$organisationAddressFix = $individualAddress->addChild('0:sfa:AddressFix');
$organisationAddressFix->addChild('0:sfa:Street', '234 Street');
$organisationAddressFix->addChild('0:sfa:BuildingIdentifier');
$organisationAddressFix->addChild('0:sfa:SuiteIdentifier');
$organisationAddressFix->addChild('0:sfa:FloorIdentifier');
$organisationAddressFix->addChild('0:sfa:DistrictName');
$organisationAddressFix->addChild('0:sfa:POB');
$organisationAddressFix->addChild('0:sfa:PostCode', '75244');
$organisationAddressFix->addChild('0:sfa:City','City');
$organisationAddressFix->addChild('0:sfa:CountrySubEntity', 'US');

$accountHolder->addChild('0:ftc:AcctHolderType','FATCA101');

$substantialOwner = $accountReport->addChild('0:ftc:SubstantialOwner');
$substantialOwner->addChild('0:sfa:ResCountryCode', 'CA');
$subOwnTIN = $substantialOwner->addChild('0:sfa:TIN', '123456789');
$subOwnTIN->addAttribute('issuedBy', 'US');
$subOwnName = $substantialOwner->addChild('0:sfa:Name');
$subOwnName->addChild('0:sfa:PrecedingTitle');
$subOwnName->addChild('0:sfa:Title');
$subOwnName->addChild('0:sfa:FirstName', 'John');
$subOwnName->addChild('0:sfa:MiddleName', 'Peter');
$subOwnName->addChild('0:sfa:NamePrefix');
$subOwnName->addChild('0:sfa:LastName', 'Smith');
$subOwnName->addChild('0:sfa:GenerationIdentifier');
$subOwnName->addChild('0:sfa:GeneralSuffix');
$subOwnAddress = $substantialOwner->addChild('0:sfa:Address');
$subOwnAddress->addChild('0:sfa:CountryCode', 'US');
$subOwnAddressFix = $subOwnAddress->addChild('0:sfa:AddressFix');
$subOwnAddressFix->addChild('0:sfa:Street','234 Street');
$subOwnAddressFix->addChild('0:sfa:BuldingIdentifier');
$subOwnAddressFix->addChild('0:sfa:SuiteIdentifier');
$subOwnAddressFix->addChild('0:sfa:FloorIdentifier');
$subOwnAddressFix->addChild('0:sfa:DistrictName');
$subOwnAddressFix->addChild('0:sfa:POB');
$subOwnAddressFix->addChild('0:sfa:PostCode', '1234');
$subOwnAddressFix->addChild('0:sfa:City');
$subOwnAddressFix->addChild('0:sfa:CountrySubEntity');
$subOwnBirthInfo = $substantialOwner->addChild('0:sfaBirthInfo');
$subOwnBirthInfo->addChild('0:sfa:BirthDate', '1960-01-01');
$subOwnBirthInfo->addChild('0:sfa:City');
$subOwnBirthInfo->addChild('0:sfa:CitySubEntity');

$accountBalance = $accountReport->addChild('0:ftc:AccountBalance', '1000.00');
$accountBalance->addAttribute('currCode', 'USD');

$payment = $accountReport->addChild('0:ftc:Payment');
$payment->addChild('Type', 'FATCA504'); 
$paymentAmnt = $payment->addChild('PaymentAmnt', '1500.00');
$paymentAmnt->addAttribute('currCode', 'USD');

header('Content-type: text/xml');
print($xml->asXML());