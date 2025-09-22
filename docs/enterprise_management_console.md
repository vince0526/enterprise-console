# Enterprise Management Console

This document captures the product requirements distilled from the Word source (DOCX). It organizes the modules, UI behavior, and key rules in a developer-friendly Markdown format. For full-fidelity layout and embedded content, see `docs/enterprise_management_console.mammoth.html`.

## Overview
The Enterprise Management Console is a database management system that maintains and controls various databases across different platforms. It provides Excel-like browsing, per-record CRUD via pop-up forms, and persistent per-table view settings.

## Navigation and Layout
- Top menu modules: Database Management, Tables and Views, File Management, Report Management, Artificial Intelligence Access, Communications, User Management, Preferences and Settings, About.
- Submodule workspace: Each module opens a workspace with its own browsing/authoring screens.
- Activity Log: A panel that can be shown/hidden and pinned; shows recent user/system actions within the active submodule.

## Core Modules

### Database Management
- Browse list of databases being managed.
- Add a new database connection via a pop-up form (CRUD) capturing: platform, host, username, password, connection string, and other best-practice metadata.
- Clicking a row opens a pop-up CRUD form to view/edit the selected database connection.
- When a database is selected, show a relational view including:
	- Database description
	- Company users of the database
	- User restrictions (allowed tables and allowed processes)
- Indicative tables involved: `DATABASECONNECTIONS`, `COMPANYUSERS`, `COMPANYUSERRESTRICTIONS`, `FTPCONNECTIONS`.

### Tables and Views
- Browse tables for the selected database; if no database is selected, the area remains blank.
- When a table is selected, show relevant details and actions (Create Table, Create Relations, Import Tables).

### File Management
- Windows Explorer–like interface over configured FTP connections.
- Actions include: Find Files, Create File, Create Folder.

### User Management
- Browse users and open a pop-up CRUD form when a row is clicked.

### Communications
- Mailbox Management (Gmail-like interface). Configure mailboxes (connection info) and manage messages.
- Artificial Intelligence screen to access providers (e.g., ChatGPT, Copilot, Grok, Gemini, Meta) with provider selection.
- Video and Audio Chat within the system.
- Spreadsheet (Excel-like) and Document management areas.

### Report Management
- Report summaries and related browsing (details to be expanded based on report definitions).

### Preferences and Settings
- Themes and screens customization; user can change colors/themes and other preferences.

## Global Browsing Behavior
- Excel-like browser in every module: sorting, filtering, checkbox selection.
- Clicking a row opens a pop-up form providing CRUD for that record.
- Numeric and other formatting functions similar to Excel.
- All settings are saved per table and can be reset to defaults.

## Filtering Logic by Field Type
The following tables formalize the filtering logic referenced in the DOCX.

### Text fields
| Filter Type | Equation / Logic | Example |
| --- | --- | --- |
| Equals | Field = "Value" | Name = "Vincent" |
| Does Not Equal | Field <> "Value" | Department <> "Finance" |
| Begins With | Field LIKE "Value%" | Name LIKE "Vin%" |
| Ends With | Field LIKE "%Value" | Email LIKE "%@gmail.com" |
| Contains | Field LIKE "%Value%" | Name LIKE "%cent%" |
| Does Not Contain | Field NOT LIKE "%Value%" | Name NOT LIKE "%cent%" |
| Is Blank / Is Not Blank | Field IS NULL / Field IS NOT NULL | Email IS NULL |

### Numeric fields
| Filter Type | Equation / Logic | Example |
| --- | --- | --- |
| Equals | Field = Value | Salary = 50000 |
| Does Not Equal | Field <> Value | Age <> 30 |
| Greater Than | Field > Value | Score > 75 |
| Less Than | Field < Value | Age < 18 |
| Greater Than or Equal | Field >= Value | Price >= 1000 |
| Less Than or Equal | Field <= Value | Quantity <= 50 |
| Between | Field BETWEEN Value1 AND Value2 | Age BETWEEN 18 AND 35 |
| Top 10 / Bottom 10 | Excel-only: dynamic filter based on ranking | Top 10 items by Sales |
| Is Blank / Is Not Blank | Field IS NULL / Field IS NOT NULL | Score IS NOT NULL |

## Activity Log
- Each submodule workspace includes an Activity Log area.
- The Activity Log can be shown/hidden and pinned so it remains visible while working.

## Persistence and Settings
- Per-table view/layout, formatting, and filtering settings are persisted.
- Users can reset settings to defaults when needed.

## Notes and Source
- This Markdown is curated from `Enterprise Management Console.docx` and associated conversions.
- For raw conversion output, see:
	- `docs/enterprise_management_console.mammoth.html` (full HTML)
	- `docs/enterprise_management_console.cleaned.md` (cleaned text extraction)

