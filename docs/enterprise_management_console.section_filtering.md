**📊 Filtering Logic by Field Type**

- Field Type | Filter Type | Filtering Equation / Logic | Example
- Text | Equals | Field \= "Value" | Name \= "Vincent"
- Does Not Equal | Field \<\> "Value" | Department \<\> "Finance"
- Begins With | Field LIKE "Value\" | Name LIKE "Vin\"
- Ends With | Field LIKE "\Value" | Email LIKE "\@gmail.com"
- Contains | Field LIKE "\Value\" | Name LIKE "\cent\"
- Does Not Contain | Field NOT LIKE "\Value\" | Name NOT LIKE "\cent\"
- Is Blank / Is Not Blank | Field IS NULL / Field IS NOT NULL | Email IS NULL

- Field Type | Filter Type | Filtering Equation / Logic | Example
- Numeric | Equals | Field \= Value | Salary \= 50000
- Does Not Equal | Field \<\> Value | Age \<\> 30
- Greater Than | Field \> Value | Score \> 75
- Less Than | Field \< Value | Age \< 18
- Greater Than or Equal | Field \>\= Value | Price \>\= 1000
- Less Than or Equal | Field \<\= Value | Quantity \<\= 50
- Between | Field BETWEEN Value1 AND Value2 | Age BETWEEN 18 AND 35
- Top 10 / Bottom 10 | Excel only: dynamic filter based on ranking | Top 10 items by Sales
- Is Blank / Is Not Blank | Field IS NULL / Field IS NOT NULL | Score IS NOT NULL

WHEN A RECORD IS CLICKED, A POP\-UP FORM COMES OUT AND PROVIDE A CRUD FUNCTION FOR THE SAID RECORD.

The browser should have formatting functions for numeric values and other features that you can perform with an Excel sheet.

All settings should be saved for a specific table that is currently edited and has an option to reset the formatting.