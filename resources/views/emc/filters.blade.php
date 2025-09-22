@extends('emc.layout')
@section('content')
<div class="content-frame">
	<div class="content">
		<h1>Filters for table: <code>{{ $table }}</code></h1>
		<p>Implements Text/Numeric filter logic as per spec.</p>
		<h2>Text fields</h2>
		<table><thead><tr><th>Filter Type</th><th>Equation / Logic</th><th>Example</th></tr></thead><tbody>
		<tr><td>Equals</td><td>Field = "Value"</td><td>Name = "Vincent"</td></tr>
		<tr><td>Does Not Equal</td><td>Field &lt;&gt; "Value"</td><td>Department &lt;&gt; "Finance"</td></tr>
		<tr><td>Begins With</td><td>Field LIKE "Value%"</td><td>Name LIKE "Vin%"</td></tr>
		<tr><td>Ends With</td><td>Field LIKE "%Value"</td><td>Email LIKE "%@gmail.com"</td></tr>
		<tr><td>Contains</td><td>Field LIKE "%Value%"</td><td>Name LIKE "%cent%"</td></tr>
		<tr><td>Does Not Contain</td><td>Field NOT LIKE "%Value%"</td><td>Name NOT LIKE "%cent%"</td></tr>
		<tr><td>Is Blank / Is Not Blank</td><td>Field IS NULL / Field IS NOT NULL</td><td>Email IS NULL</td></tr>
		</tbody></table>

		<h2>Numeric fields</h2>
		<table><thead><tr><th>Filter Type</th><th>Equation / Logic</th><th>Example</th></tr></thead><tbody>
		<tr><td>Equals</td><td>Field = Value</td><td>Salary = 50000</td></tr>
		<tr><td>Does Not Equal</td><td>Field &lt;&gt; Value</td><td>Age &lt;&gt; 30</td></tr>
		<tr><td>Greater Than</td><td>Field &gt; Value</td><td>Score &gt; 75</td></tr>
		<tr><td>Less Than</td><td>Field &lt; Value</td><td>Age &lt; 18</td></tr>
		<tr><td>Greater Than or Equal</td><td>Field &gt;= Value</td><td>Price &gt;= 1000</td></tr>
		<tr><td>Less Than or Equal</td><td>Field &lt;= Value</td><td>Quantity &lt;= 50</td></tr>
		<tr><td>Between</td><td>Field BETWEEN Value1 AND Value2</td><td>Age BETWEEN 18 AND 35</td></tr>
		<tr><td>Top 10 / Bottom 10</td><td>Excel-only: dynamic filter based on ranking</td><td>Top 10 items by Sales</td></tr>
		<tr><td>Is Blank / Is Not Blank</td><td>Field IS NULL / Field IS NOT NULL</td><td>Score IS NOT NULL</td></tr>
		</tbody></table>
	</div>
</div>
@endsection
