@php

$TransactionId = intval("55" . rand(1, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9));
$MerchantId = "5101";
$TerminalId = "30114";
$ApiPassword = "2Nbp1jKUAia";
$private_key = "QTry8Ka5EnHFi0Ju2AdhzSRt";
$signature_key = trim($private_key . $ApiPassword . $TransactionId);
$ApiPassword_encrypt = hash('sha256', $ApiPassword);
$ReturnUrl = route('gatewayservice-return');
$xmlReq = '<?xml version="1.0" encoding="UTF-8"?>
 <TransactionRequest>    
 <Language>ENG</Language>  
   <Credentials>
   <MerchantId>' . $MerchantId . '</MerchantId>
    <TerminalId>' . $TerminalId . '</TerminalId>
    <TerminalPassword>' . $ApiPassword_encrypt . '</TerminalPassword>
     </Credentials> 
     <ReturnUrl page="'.$ReturnUrl.'">
      <Param>
      <Key>inv</Key>
      <Value>83785</Value>
      </Param>
      </ReturnUrl>
     <TransactionType>LP001</TransactionType>
     <TransactionId>' . $TransactionId . '</TransactionId>
       <CurrencyCode>EUR</CurrencyCode>
       <TotalAmount>100</TotalAmount>
       <ProductDescription>TShirt</ProductDescription>
       <CustomerDetails>
			<FirstName>test</FirstName>
			<LastName>test</LastName>
			<CustomerIP>122.170.41.196</CustomerIP>
			<Phone>989816524444</Phone>
			<Email>test@gmail.com</Email>
			<Street>test</Street>
			<City>test</City>
			<Region>test</Region>
			<Country>US</Country> 
			<Zip>12345</Zip>
       </CustomerDetails>
	   <CardDetails>
			<CardHolderName>test</CardHolderName>
			<CardNumber>4242424242424242</CardNumber>
			<CardExpireMonth>02</CardExpireMonth>
			<CardExpireYear>2025</CardExpireYear>
			<CardType>VI</CardType>
			<CardSecurityCode>123</CardSecurityCode>
	   </CardDetails>

</TransactionRequest>';
$signature = base64_encode(hash_hmac("sha256", trim($xmlReq), $signature_key, True));

$encodedMessage = base64_encode($xmlReq);

@endphp

<form method="POST" action="https://test.gateway-services.com/acquiring.php">
    <input type="hidden" name="version" value="1.0"/>
    <input type="hidden" name="encodedMessage" value="<?php echo $encodedMessage ?>">
    <input type="hidden" name="signature" value="<?php echo $signature ; ?>">
    <button type="submit">Submit</button>
</form>





