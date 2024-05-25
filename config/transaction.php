<?php

return [
	'success_count' => "sum(if(t.status = '1', 1, 0))",
	'success_amount' => "sum(if(t.status = '1', amount, 0.00))",
	'success_percentage' => "round((100*sum(if(t.status = '1', 1, 0)))/(sum(if(t.status = '0', 1, 0))+sum(if(t.status = '1', 1, 0))) , 2)",
	'declined_amount' => "sum(if(t.status = '0' , amount,0.00 ))",
	'declined_count' => "sum(if(t.status = '0', 1, 0))",
	'declined_percentage' => "round((100*sum(if(t.status = '0', 1, 0)))/(sum(if(t.status = '0', 1, 0))+sum(if(t.status = '1', 1, 0))) ,2)",
	'chargeback_amount' => "sum(if(t.status = '1' and t.chargebacks = '1' and t.chargebacks_remove = '0', amount, 0))",
	'chargeback_count' => "sum(if(t.status = '1' and t.chargebacks = '1' and t.chargebacks_remove = '0', 1, 0))",
	'chargeback_percentage' => "round((100*sum(if(t.status = '1' and t.chargebacks = '1' and t.chargebacks_remove = '0', 1, 0)))/sum(if(t.status = '1', 1, 0)) ,2)",
	'suspicious_count' => "sum(if(t.status = '1' and t.is_flagged = '1' and t.is_flagged_remove= '0', 1, 0))",
	'suspicious_amount' => "sum(if(t.status = '1' and t.is_flagged = '1' and t.is_flagged_remove= '0', amount, 0))",
	'suspicious_percentage' => "round((100*sum(if(t.status = '1' and t.is_flagged = '1' and t.is_flagged_remove= '0', 1, 0)))/sum(if(t.status = '1', 1, 0)) ,2)",
	'refund_count' => "sum(if(t.status = '1' and t.refund = '1' and t.refund_remove='0', 1, 0))",
	'refund_amount' => "sum(if(t.status = '1' and t.refund = '1' and t.refund_remove='0', amount, 0))",
	'refund_percentage' => "round((100*sum(if(t.status = '1' and t.refund = '1' and t.refund_remove='0', 1, 0)))/sum(if(t.status = '1', 1, 0)) ,2)"
];

?>