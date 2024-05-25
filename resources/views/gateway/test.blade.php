<form  action="https://dashboard.qartpay.com/crm/jsp/merchantpay" method="post" style="margin-top: 0.5em;" name="paymentForm">
    <input type="text" name="AMOUNT" value="200"/>
    <input type="text" name="CURRENCY_CODE" value="356"/>
    <input type="text" name="CUST_EMAIL" value="test@yopmail.com"/>
    <input type="text" name="CUST_NAME" value="test test"/>
    <input type="text" name="CUST_PHONE" value="9876543232"/>
    <input type="text" name="MOP_TYPE" value="VI"/>
    <input type="text" name="ORDER_ID" value="167349832824EWRAS"/>
    <input type="text" name="PAY_ID" value="2109141501532770"/>
    
    <input type="text" name="PAYMENT_TYPE" value="DC"/>
    <input type="text" name="PRODUCT_DESC" value="Pay By"/>
    <input type="text" name="RETURN_URL" value="https://webhook.site/b2e9fd9e-cc8f-4dc0-8fcb-4b6f831ce385"/>
    <input type="text" name="TXNTYPE" value="SALE"/>
    <input type="text" name="CARD_NUMBER" value="4304636301077885"/>
    <input type="text" name="CARD_EXP_DT" value="012022"/>
    <input type="text" name="CVV" value="936"/>
    <input type="text" name="HASH" value="6843B835985778633F7016A139F20C64CFE2B3831D61983BA98B56294B663C94"/>
</form>
<script src="//code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script type="text/javascript">
   
    window.onload = function () {
        document.paymentForm.submit();
    }
</script>