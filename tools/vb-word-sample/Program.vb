Imports Xceed.Words.NET

Module Program
    Sub Main()
        Dim input = "sample.docx"
        Dim output = "sample-edited.docx"

        If Not System.IO.File.Exists(input) Then
            Console.WriteLine($"Input file not found: {input}")
            Console.WriteLine("Copy docs/modules.docx to this folder as sample.docx and re-run.")
            Return
        End If

        Using doc = DocX.Load(input)
            ' Replace text (simple)
            doc.ReplaceText("Enterprise Console", "Enterprise Console (edited)")

            ' Add a heading
            doc.InsertParagraph().InsertText("VB Sample Edit").FontSize(16).Bold().SpacingAfter(6)

            ' Insert a simple table (3x3)
            Dim table = doc.AddTable(3, 3)
            table.Design = TableDesign.LightShadingAccent1
            For r As Integer = 0 To 2
                For c As Integer = 0 To 2
                    table.Rows(r).Cells(c).Paragraphs(0).Append($"R{r+1}C{c+1}")
                Next
            Next
            doc.InsertTable(table)

            ' Insert an image if present
            Dim imagePath = "sample-image.png"
            If System.IO.File.Exists(imagePath) Then
                Dim img = doc.AddImage(imagePath)
                Dim pic = img.CreatePicture()
                doc.InsertParagraph().AppendPicture(pic)
            End If

            ' Demonstrate rich replace (preserve case-insensitive)
            doc.ReplaceText("logged in", "signed in", False, System.Text.RegularExpressions.RegexOptions.IgnoreCase)

            ' Append footer paragraph with timestamp
            doc.InsertParagraph().InsertText("Edited by VB sample on " & DateTime.Now.ToString()).Italic()

            ' Save
            doc.SaveAs(output)
            Console.WriteLine($"Saved: {output}")
        End Using
    End Sub
End Module
