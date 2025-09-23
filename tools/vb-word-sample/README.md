VB.NET Word editing sample (DocX)

This sample uses Xceed.Words.NET (DocX) to open an existing .docx, perform edits (replace text, insert headings, create tables, embed images), and save a new file.

Requirements
- .NET SDK (6.0+) or Visual Studio
- NuGet package: Xceed.Words.NET

Run (PowerShell)

cd tools/vb-word-sample
dotnet restore
dotnet run --project VbWordSample.vbproj

Run with automatic fallback (PowerShell wrapper)

From repository root you can run the wrapper which will attempt to run the VB sample if the .NET SDK is available, otherwise it will run the Python fallback:

```powershell
cd C:\laragon\www\enterprise-console
powershell -ExecutionPolicy Bypass -File tools\vb-word-sample\run-sample.ps1
```

Files
- VbWordSample.vbproj: project file referencing Xceed.Words.NET
- Program.vb: sample code (advanced examples: heading, table, image, replace)
- sample.docx: example input (not included) — provide your own or copy docs/modules.docx to this folder
- sample-image.png: optional image to demonstrate image insertion (place a PNG in this folder)

Notes
- This sample uses DocX because it does not require MS Word to be installed.
- For production, consider DocumentFormat.OpenXml for heavier processing or commercial libraries for advanced features (GemBox, Aspose).

Advanced examples in Program.vb
- Heading insertion: adds a title and spacing
- Table creation: inserts a simple 3x3 table with basic styling (TableDesign presets)
- Image insertion: if a file named `sample-image.png` exists in the folder, it will be embedded
- Rich replace: example of case-insensitive replace using regex options

Tips
- Images: use reasonable dimensions; DocX embeds the image as-is.
- Tables: DocX offers several `TableDesign` presets; adjust styling via cell paragraphs.

If you want more examples (tables with merged cells, inserting images from URLs, or style manipulation), I can add them.
