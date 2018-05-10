<html>
	<head>
		<meta charset="utf-8">
		<title>Invoice</title>
	</head>
	<body onload="parseAll()">
        <style>
                *
                {
                    border: 0;
                    box-sizing: content-box;
                    color: inherit;
                    font-family: inherit;
                    font-size: inherit;
                    font-style: inherit;
                    font-weight: inherit;
                    line-height: inherit;
                    list-style: none;
                    margin: 0;
                    padding: 0;
                    text-decoration: none;
                    vertical-align: top;
                }
                
                /* heading */
                
                h1 { font: bold 100% sans-serif; letter-spacing: 0.5em; text-align: center; text-transform: uppercase; }
                
                /* table */
                
                table { font-size: 75%; table-layout: fixed; width: 100%; }
                table { border-collapse: separate; border-spacing: 2px; }
                th, td { border-width: 1px; padding: 0.5em; position: relative; text-align: left; }
                th, td { border-radius: 0.25em; border-style: solid; }
                th { background: #EEE; border-color: #BBB; }
                td { border-color: #DDD; }
                
                /* page */
                
                html { font: 16px/1 'Open Sans', sans-serif; overflow: auto; padding: 0.5in; }
                html { background: #999; cursor: default; }
                
                body { box-sizing: border-box; height: 11in; margin: 0 auto; overflow: hidden; padding: 0.5in; width: 8.5in; }
                body { background: #FFF; border-radius: 1px; box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5); }
                
                /* header */
                
                header { margin: 0 0 3em; }
                header:after { clear: both; content: ""; display: table; }
                
                header h1 { background: #000; border-radius: 0.25em; color: #FFF; margin: 0 0 1em; padding: 0.5em 0; }
                header address { float: left; font-size: 75%; font-style: normal; line-height: 1.25; margin: 0 1em 1em 0; }
                header address p { margin: 0 0 0.25em; }
                header span, header img { display: block; float: right; }
                header span { margin: 0 0 1em 1em; max-height: 25%; max-width: 60%; position: relative; }
                header img { max-height: 100%; max-width: 100%; }
                header input { cursor: pointer; -ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)"; height: 100%; left: 0; opacity: 0; position: absolute; top: 0; width: 100%; }
                
                /* article */
                
                article, article address, table.inv_detail, table.inventory { margin: 0 0 3em; }
                article:after { clear: both; content: ""; display: table; }
                article h1 { clip: rect(0 0 0 0); position: absolute; }
                
                article address { float: right; font-size: 125%; font-weight: bold; }
                
                /* table meta & balance */
                
                table.balance { float: right; width: 36%;font-size: 13px;}
                table.inv_detail {float:left; width: 36%; background: #88bcc1; font-size: 13px; border-radius: 6px; padding-left: 10px;}
                table.inv_detail:after, table.balance:after { clear: both; content: ""; }
                
                /* table meta */
                
                table.inv_detail th { width: 40%; border: none; background: none;font-weight: 600}
                table.inv_detail td { width: 60%; border: none; background: none;}
                
                /* table items */
                
                table.inventory { clear: both; width: 100%; }
                table.inventory th { font-weight: bold; text-align: center; background: #88bcc1}
                
                table.inventory td:nth-child(1), table.inventory th:nth-child(1) { width: 6%; text-align: center}
                table.inventory td:nth-child(2), table.inventory th:nth-child(2) { width: 15%; text-align: center }
                table.inventory th:nth-child(4) { width: 20%}
                table.inventory td:nth-child(4) { text-align: right}
                
                /* table balance */
                
                table.balance th { width: 50%; font-weight: 600}
                table.balance td { width: 50%; text-align: right; }
                
                /* aside */
                
                aside h1 { border: none; border-width: 0 0 1px; margin: 0 0 1em; }
                aside h1 { border-color: #999; border-bottom-style: solid; }
                
                .print-btn{padding: 10px 25px;border: solid 2px #88bcc1;background: #bfd6d8;border-radius: 6px;font-size: 15px;font-weight: 600;color: #0b3f44;opacity: .75}
                .print-btn:hover{opacity: 1}
                .print-area {text-align: center; padding-top: 20px;border: none}
                /* javascript */
                
                @media print {
                    * { -webkit-print-color-adjust: exact; }
                    html { background: none; padding: 0; }
                    body { box-shadow: none; margin: 0; }
                    span:empty { display: none; }
                    .print-btn {visibility: hidden}
                }
                
                @page { margin: 0; }
        </style>
		<article>
			<h1>Recipient</h1>
			<address >
				<p>{{$store}}</p>
			</address>
			<table class="inv_detail">
				<tr>
					<th><span >Invoice #</span></th>
					<td><span >: {{$transaction->invoice}}</span></td>
				</tr>
				<tr>
					<th><span >Date</span></th>
					<td><span >: {{\Carbon\Carbon::parse($transaction->date)->format('F d, Y')}}</span></td>
				</tr>
				<tr>
					<th><span >Amount Due</span></th>
					<td><span id="prefix" >: Rp</span> <span class="priced">{{$sum}}</span></td>
				</tr>
			</table>
			<table class="inventory">
				<thead>
					<tr>
						<th><span >Qty</span></th>
						<th><span >Rate</span></th>
						<th><span >Name</span></th>
						<th><span >Price</span></th>
					</tr>
				</thead>
				<tbody>
                    @foreach ($productsList as $product)
					<tr>
						<td><span >{{$product['qty']}}</span></td>
						<td><span data-prefix>Rp</span> <span class="priced">{{$product['price']}}</span></td>
						<td><span >{{$product['name']}}</span></td>
						<td><span data-prefix>Rp</span> <span class="priced">{{$product['price'] * $product['qty']}}</span></td>
                    </tr>
                    @endforeach
				</tbody>
			</table>
			<table class="balance">
				<tr>
					<th><span >Total</span></th>
					<td><span data-prefix>Rp</span> <span id="total"  class="priced">{{$sum}}</span></td>
				</tr>
				<tr>
					<th><span >Amount Paid</span></th>
					<td><span data-prefix>Rp</span> <span id="amount_paid" contenteditable>00</span></td>
				</tr>
				<tr>
					<th><span >Balance Due</span></th>
					<td><span data-prefix>Rp</span> <span id="balance_due">{{-$sum}}</span></td>
                </tr>
                <tr>
                    <td colspan="2" class="print-area" >
                        <button class="print-btn" onclick="printThis()">PRINT</button>
                    </td>
                </tr>
            </table>
            
        </article>
        <script>
            var due = document.getElementById('balance_due');
            var total = document.getElementById('total');
            var paid = document.getElementById('amount_paid');
            function parseAll(){
                var elems = document.getElementsByClassName('priced');
                for (i=0; i<elems.length; i++){
                    elems[i].innerHTML=parsePrice(parseInt(elems[i].innerHTML));
                };
            };
            function printThis(){
                if ((parseInt(paid.innerHTML)<=0)||(parseInt(due.innerHTML)<0)){
                    paid.focus();
                }
                else {
                    paid.innerHTML = parsePrice(parseInt(paid.innerHTML));
                    window.print();
                }
            }
            function updateField(){
                due.innerHTML= parsePrice(parseInt(paid.innerHTML) - parseInt(total.innerHTML)*1000);
            }
            function parsePrice(number) {
                return number.toFixed().replace(/(\d)(?=(\d{3})+(,|$))/g, '$1.');
            }
            
            document.addEventListener('keydown', updateField);
		    document.addEventListener('keyup', updateField);
        </script>
	</body>
</html>