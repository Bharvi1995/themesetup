CREATE TRIGGER `Tx_Transaction_Update` AFTER UPDATE ON `transactions`
 FOR EACH ROW Update tx_transactions 
set TXs=(SELECT sum(if(t.status = '1', 1, 0)) as TXs from transactions t 
			left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),
	TXsP = (SELECT (sum(if(t.status = '1', 1, 0)*100)/sum(if(t.status = '1' or t.status = '0', 1, 0))) as TXsP from transactions t 
			left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),
	VOLs = (SELECT sum(if(t.status = '1', amount, 0.00)) as VOLs from transactions t 
			left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),
	TXd =  (SELECT sum(if(t.status = '0', 1, 0)) as TXd from transactions t 
			left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),
            TXdP = (SELECT (sum(if(t.status = '0', 1, 0)*100)/sum(if(t.status = '1' or t.status = '0', 1, 0))) as TXdP from transactions t 
			left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),
	VOLd = (SELECT sum(if(t.status = '0' , amount,0.00 )) as VOLd from transactions t 
			left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),
	CBTX = (SELECT sum(if(t.status = '1' and t.chargebacks = '1' and t.chargebacks_remove = '0', 1, 0)) as CBTX from transactions t left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),
	
	CBTXP = (SELECT (sum(if(t.status = '1' and t.chargebacks = '1' and t.chargebacks_remove = '0', 1, 0)*100)/sum(if(t.status = '1', 1, 0))) as CBTXP from transactions t left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),
	
	CBV =  (SELECT sum(if(t.status = '1' and t.chargebacks = '1' and t.chargebacks_remove = '0', amount, 0)) as CBV from transactions t left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),
	
	REFTX = (SELECT sum(if(t.status = '1' and t.refund = '1' and t.refund_remove='0', 1, 0)) as REFTX from transactions t left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),
	
	REFTXP = (SELECT (sum(if(t.status = '1' and t.refund = '1' and t.refund_remove = '0', 1, 0)*100)/sum(if(t.status = '1', 1, 0))) as REFTXP from transactions t left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),
	
	REFV = (SELECT sum(if(t.status = '1' and t.refund = '1' and t.refund_remove='0', amount, 0)) as REFV from transactions t left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),

	FLGTX = (SELECT sum(if(t.status = '1' and t.is_flagged = '1' and t.is_flagged_remove= '0', 1, 0)) as FLGTX from transactions t left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),

	FLGTXP = (SELECT (sum(if(t.status = '1' and t.is_flagged = '1' and t.is_flagged_remove= '0', 1, 0)*100)/sum(if(t.status = '1', 1, 0))) as FLGTXP from transactions t left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),

	FLGV = (SELECT sum(if(t.status = '1' and t.is_flagged = '1' and t.is_flagged_remove= '0', amount, 0)) as FLGV from transactions t left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),

	RETTX = (SELECT sum(if(t.status = '1' and t.is_retrieval  = '1' and t.is_retrieval_remove= '0', 1, 0)) as RETTX from transactions t left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),

	RETTXP = (SELECT (sum(if(t.status = '1' and t.is_retrieval = '1' and t.is_retrieval_remove= '0', 1, 0)*100)/sum(if(t.status = '1', 1, 0))) as RETTXP from transactions t left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),

	RETV = (SELECT sum(if(t.status = '1' and t.is_retrieval  = '1' and t.is_retrieval_remove= '0', amount, 0)) as RETV from transactions t left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),

	TXb =  (SELECT sum(if(t.status = '5', 1, 0)) as TXb from transactions t left join users  u on u.id = 
			t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),

	TXbP = (SELECT (sum(if(t.status = '5', 1, 0)*100)/sum(if(t.status = '1', 1, 0))) as TXbP from transactions t left join users  u on u.id = 
			t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),

	VOLb = (SELECT sum(if(t.status = '5', amount, 0.00)) as VOLb from transactions t left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency)

where user_id = new.user_id and currency = new.currency and DATE(transaction_date) = DATE(new.transaction_date);







CREATE TRIGGER `Tx_Transaction_Insert` AFTER INSERT ON `transactions`
 FOR EACH ROW IF (1 <= (SELECT count(*)
 as checkcondition FROM tx_transactions where user_id = new.user_id and currency = new.currency and DATE(transaction_date) = DATE(new.transaction_date))) THEN 
 
Update tx_transactions 
set TXs=(SELECT sum(if(t.status = '1', 1, 0)) as TXs from transactions t 
			left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),
	TXsP = (SELECT (sum(if(t.status = '1', 1, 0)*100)/sum(if(t.status = '1' or t.status = '0', 1, 0))) as TXsP from transactions t 
			left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),
	VOLs = (SELECT sum(if(t.status = '1', amount, 0.00)) as VOLs from transactions t 
			left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),
	TXd =  (SELECT sum(if(t.status = '0', 1, 0)) as TXd from transactions t 
			left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),
            TXdP = (SELECT (sum(if(t.status = '0', 1, 0)*100)/sum(if(t.status = '1' or t.status = '0', 1, 0))) as TXdP from transactions t 
			left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),
	VOLd = (SELECT sum(if(t.status = '0' , amount,0.00 )) as VOLd from transactions t 
			left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),
	CBTX = (SELECT sum(if(t.status = '1' and t.chargebacks = '1' and t.chargebacks_remove = '0', 1, 0)) as CBTX from transactions t left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),
	
	CBTXP = (SELECT (sum(if(t.status = '1' and t.chargebacks = '1' and t.chargebacks_remove = '0', 1, 0)*100)/sum(if(t.status = '1', 1, 0))) as CBTXP from transactions t left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),
	
	CBV =  (SELECT sum(if(t.status = '1' and t.chargebacks = '1' and t.chargebacks_remove = '0', amount, 0)) as CBV from transactions t left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),
	
	REFTX = (SELECT sum(if(t.status = '1' and t.refund = '1' and t.refund_remove='0', 1, 0)) as REFTX from transactions t left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),
	
	REFTXP = (SELECT (sum(if(t.status = '1' and t.refund = '1' and t.refund_remove = '0', 1, 0)*100)/sum(if(t.status = '1', 1, 0))) as REFTXP from transactions t left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),
	
	REFV = (SELECT sum(if(t.status = '1' and t.refund = '1' and t.refund_remove='0', amount, 0)) as REFV from transactions t left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),

	FLGTX = (SELECT sum(if(t.status = '1' and t.is_flagged = '1' and t.is_flagged_remove= '0', 1, 0)) as FLGTX from transactions t left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),

	FLGTXP = (SELECT (sum(if(t.status = '1' and t.is_flagged = '1' and t.is_flagged_remove= '0', 1, 0)*100)/sum(if(t.status = '1', 1, 0))) as FLGTXP from transactions t left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),

	FLGV = (SELECT sum(if(t.status = '1' and t.is_flagged = '1' and t.is_flagged_remove= '0', amount, 0)) as FLGV from transactions t left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),

	RETTX = (SELECT sum(if(t.status = '1' and t.is_retrieval  = '1' and t.is_retrieval_remove= '0', 1, 0)) as RETTX from transactions t left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),

	RETTXP = (SELECT (sum(if(t.status = '1' and t.is_retrieval = '1' and t.is_retrieval_remove= '0', 1, 0)*100)/sum(if(t.status = '1', 1, 0))) as RETTXP from transactions t left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),

	RETV = (SELECT sum(if(t.status = '1' and t.is_retrieval  = '1' and t.is_retrieval_remove= '0', amount, 0)) as RETV from transactions t left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),

	TXb =  (SELECT sum(if(t.status = '5', 1, 0)) as TXb from transactions t left join users  u on u.id = 
			t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),

	TXbP = (SELECT (sum(if(t.status = '5', 1, 0)*100)/sum(if(t.status = '1', 1, 0))) as TXbP from transactions t left join users  u on u.id = 
			t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency),

	VOLb = (SELECT sum(if(t.status = '5', amount, 0.00)) as VOLb from transactions t left join users  u on u.id = t.user_id where (u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency =new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency)

where user_id = new.user_id and currency = new.currency and DATE(transaction_date) = DATE(new.transaction_date);

 ELSE 
 
 INSERT INTO tx_transactions

		    SELECT 
				t.id,t.user_id,u.name,t.currency,u.agent_id,t.payment_gateway_id,t.status,
				sum(if(t.status = '1', 1, 0)) TXs,
				(sum(if(t.status = '1', 1, 0)*100)/sum(if(t.status = '1' or t.status = '0', 1, 0))) TXsP,
			 	sum(if(t.status = '1', amount, 0.00)) as VOLs,
			 	sum(if(t.status = '0', 1, 0)) as TXd,
			 	(sum(if(t.status = '0', 1, 0)*100)/sum(if(t.status = '1' or t.status = '0', 1, 0))) TXdP,
			 	sum(if(t.status = '0' , amount,0.00 )) as VOLd,
			 	sum(if(t.status = '1' and t.chargebacks = '1' and t.chargebacks_remove = '0', 1, 0)) CBTX,
			 	(sum(if(t.status = '1' and t.chargebacks = '1' and t.chargebacks_remove = '0', 1, 0)*100)/sum(if(t.status = '1', 1, 0))) CBTXP,
			 	sum(if(t.status = '1' and t.chargebacks = '1' and t.chargebacks_remove = '0', amount, 0)) as CBV,
			 	sum(if(t.status = '1' and t.refund = '1' and t.refund_remove='0', 1, 0)) REFTX,
			 	(sum(if(t.status = '1' and t.refund = '1' and t.refund_remove = '0', 1, 0)*100)/sum(if(t.status = '1', 1, 0))) REFTXP,
			 	sum(if(t.status = '1' and t.refund = '1' and t.refund_remove='0', amount, 0)) as REFV,
			 	sum(if(t.status = '1' and t.is_flagged = '1' and t.is_flagged_remove= '0', 1, 0)) FLGTX,
			 	(sum(if(t.status = '1' and t.is_flagged = '1' and t.is_flagged_remove= '0', 1, 0)*100)/sum(if(t.status = '1', 1, 0))) FLGTXP,
				sum(if(t.status = '1' and t.is_flagged = '1' and t.is_flagged_remove= '0', amount, 0)) as FLGV,
				sum(if(t.status = '1' and t.is_retrieval  = '1' and t.is_retrieval_remove= '0', 1, 0)) RETTX,
				(sum(if(t.status = '1' and t.is_retrieval = '1' and t.is_retrieval_remove= '0', 1, 0)*100)/sum(if(t.status = '1', 1, 0))) RETTXP,
				sum(if(t.status = '1' and t.is_retrieval  = '1' and t.is_retrieval_remove= '0', amount, 0)) as RETV,
				sum(if(t.status = '5', 1, 0)) TXb,
				(sum(if(t.status = '5', 1, 0)*100)/sum(if(t.status = '1', 1, 0))) TXbP,
				sum(if(t.status = '5', amount, 0.00)) as VOLb,
				t.transaction_date,
			 	NOW(),
			 	NOW()
		 	from transactions t 
			left join users  u on u.id = t.user_id where 
		(u.id is null or u.id is not null) and t.user_id = new.user_id and t.currency = new.currency and DATE(t.transaction_date) = DATE(new.transaction_date) group by DATE(t.created_at), t.user_id, t.currency;
 
 END IF