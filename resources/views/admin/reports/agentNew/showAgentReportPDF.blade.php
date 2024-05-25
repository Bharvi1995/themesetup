<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title></title>
	<style type="text/css" media="screen">
		.clearfix:after {
		  content: "";
		  display: table;
		  clear: both;
		}

		a {
		  color: #5D6975;
		  text-decoration: underline;
		}

		body {
		  position: relative;
		  width: 21cm;  
		  height: 29.7cm; 
		  margin: 0 auto; 
		  color: #001028;
		  background: #FFFFFF; 
		  font-family: Arial, sans-serif; 
		  font-size: 12px; 
		  font-family: Arial;
		}

		header {
		  padding: 10px 0;
		  margin-bottom: 30px;
		}

		#logo {
		  /*text-align: center;*/
		  margin-bottom: 0px;
		}

		#logo img {
		  width: 200px;
		}
		.title {
			  border-top: 1px solid  #5D6975;
			  border-bottom: 1px solid  #5D6975;
			  color: #5D6975;
			  font-weight: normal;
			  text-align: center;
			  width: 100%;
			  margin-bottom: 20px;
			  padding: 10px 0px;
		}
		.title img {
			width: 15%;
			float:left;
		}
		.title h1 {
			font-size: 2.4em;
		  	line-height: 1.4em;
		  	font-weight: normal;
		  	text-align: left;
			margin: 0 0 0px 0;
		}
		#from {
			float: left;
			width: 33.33%;
			font-size: 14px;
			color: #5D6975;
		}
		#project {
		  float: left;
		  width: 50%;
		  font-size: 14px;
		  color: #5D6975;
		}

		#project span {
		  color: #5D6975;
		  text-align: right;
		  width: 52px;
		  margin-right: 10px;
		  display: inline-block;
		  font-size: 0.8em;
		}

		#company {
		  float: left;
		  text-align: left;
		  text-align: left;
		  width: 50%;
		  font-size: 14px;
		  color: #5D6975;
		}

		#project div,
		#company div {
			padding-left: 15px;
		  white-space: nowrap;        
		}

		table {
		  width: 100%;
		  border-collapse: collapse;
		  border-spacing: 0;
		  margin-bottom: 20px;
		}

		table tr:nth-child(2n-1) td {
		  background: #F5F5F5;
		}

		table th,
		table td {
		  text-align: center;
		}

		table thead {
		}

		table th {
		  padding: 10px 20px;
		  color: #5D6975;
		  border-bottom: 1px solid #C1CED9;
		  white-space: nowrap;        
		  font-weight: 900;
		}

		table .service,
		table .desc {
		  text-align: left;
		}

		table td {
		  padding: 5px 20px;
		  text-align: right;
		}

		table td.service,
		table td.desc {
		  vertical-align: top;
		}

		table td.unit,
		table td.qty,
		table td.total {
		  font-size: 1.2em;
		}

		table td.grand {
		  border-top: 1px solid #5D6975;;
		}

		#notices .notice {
		  color: #5D6975;
		  font-size: 1.2em;
		}

		footer {
		  color: #5D6975;
		  width: 100%;
		  height: 30px;
		  /*position: absolute;*/
		  bottom: 0;
		  border-top: 1px solid #C1CED9;
		  padding: 8px 0;
		  text-align: center;
		}
		.center {
			text-align: center;
		}
		.right {
			text-align: right;
		}
		.bluebg td{
			background: #CCE5FF !important;
		}
		.greenbg td{
			background: #CCFFCC !important;
		}
		.redbg td{
			background: #FFE5CC !important;
		}
	</style>
</head>
<body>
	<header class="clearfix">
	  	<div id="logo">
	  	</div>
	  	<div class="title">
        	<div><strong>{{ $data->company_name }}</strong></div>
	  	</div>
      	<div id="project">
        	<div><strong>Settlement Date</strong> {{ $data->start_date }} to {{ $data->end_date }}</div>
        	<div><strong>Settlement No.</strong> {{ $data->report_no }}</div>
      	</div>
    </header>
    <main>
	    <?php $i=1; ?>
	    @foreach($childData as $key => $value)
	    <table>
	    	<thead>
	    		<tr>
	            	<th colspan="2"><h3><strong>Currency : {{ $value->currency }}</strong></h3></th>
	          	</tr>
	    	</thead>
	    	<tbody>
	    		<tr>
	    			<td class="center">Success Amount</td>
	    			<td class="right">{{ round($value->success_amount, 2) }}</td>
	    		</tr>
	    		<tr>
	    			<td class="center">Success Count</td>
	    			<td class="right">{{ round($value->success_count, 2) }}</td>
	    		</tr>
	    		<tr>
	    			<td class="center">Commission Percentage</td>
	    			<td class="right">{{ round($value->commission_percentage, 2) }}%</td>
	    		</tr>
	    		<tr>
	    			<td class="center">Total Commission</td>
	    			<td class="right">{{ round($value->total_commission, 2) }}</td>
	    		</tr>
	    	</tbody>
	    </table>
	    <?php $i++; ?>
	    @endforeach
    </main>
</body>
</html>