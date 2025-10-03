// @ts-nocheck
import React, { useEffect, useMemo, useState } from "react";
import {
    Badge,
    BookOpenText,
    Button,
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    Checkbox,
    CheckCircle2,
    ChevronRight,
    CommandDialog,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
    CommandSeparator,
    Database,
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    Download,
    Eye,
    FilePlus2,
    Filter,
    HelpCircle,
    Info,
    Input,
    Keyboard,
    Label,
    ScrollArea,
    Search,
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
    Separator,
    Sparkles,
    Switch,
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
    Tabs,
    TabsContent,
    TabsList,
    TabsTrigger,
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from "./ui-stubs";

/**
 * Core Databases Workbench — Stage-first Value-Chain builder + registry + guide
 * Order enforced: Stage → Industry → Subindustry
 */

// -----------------------------
// Types
// -----------------------------
type Tier = "Value Chain" | "Public Goods & Governance" | "CSO" | "Media" | "Financial";
type Engine = "PostgreSQL" | "MySQL" | "SQL Server";
type Env = "Dev" | "UAT" | "Prod";

type DbRecord = {
  id: string;
  name: string;
  engine: Engine;
  environment: Env;
  tier: Tier;
  path: string; // e.g., Value Chain → Stage → Industry → Subindustry
  scopes: string[]; // e.g., Accounting, Inventory, ...
  owner: string;
  status: "Healthy" | "Degraded" | "Decommissioned";
  createdAt: string; // YYYY-MM-DD
  updatedAt: string; // YYYY-MM-DD
};

// -----------------------------
// Value-Chain Stack & Cross-Cutting Enablers
// -----------------------------
export type VCStageKey =
  | "Resource Extraction (Primary)"
  | "Primary Processing (Materials)"
  | "Secondary Manufacturing & Assembly"
  | "Market Access, Trading & Wholesale"
  | "Logistics, Ports & Fulfillment"
  | "Retail & Direct-to-Consumer (Goods)"
  | "Service Delivery (End-User Services)"
  | "After-Sales, Reverse & End-of-Life";

const VC_STACK: Record<
  VCStageKey,
  { what: string; industries: string[]; leadComplexity: string; dependencies: string[]; regulatory: string[] }
> = {
  "Resource Extraction (Primary)": {
    what: "Obtain natural inputs.",
    industries: ["Mining & quarrying", "Oil & gas", "Agriculture & aquaculture", "Forestry", "Fisheries"],
    leadComplexity: "Very long asset cycles; high capex; price volatility; site-specific risk.",
    dependencies: ["Land/water rights", "Geology & climate", "Energy", "Permits", "Local acceptance", "Infrastructure"],
    regulatory: ["Concessions/licensing", "ESIA", "Labor & safety", "Royalties & fiscal regimes", "Export permits", "Biodiversity & indigenous rights"],
  },
  "Primary Processing (Materials)": {
    what: "Turn raw inputs into standard intermediates.",
    industries: [
      "Refineries",
      "Petrochemicals",
      "Steel & non-ferrous smelting",
      "Cement & glass",
      "Pulp/paper & sawmills",
      "Primary food processing",
      "Basic chemicals",
      "Synthetic fibers",
    ],
    leadComplexity: "Continuous, capital-intensive; energy-intensive; economies of scale.",
    dependencies: ["Stable feedstock", "Utilities (power, water, steam)", "HAZMAT handling", "Bulk logistics"],
    regulatory: ["Emissions & effluents", "Industrial safety (Seveso/PSM)", "Food safety standards", "Cross-border chemicals & waste"],
  },
  "Secondary Manufacturing & Assembly": {
    what: "Convert intermediates into finished goods and capital equipment.",
    industries: [
      "Automotive",
      "Aerospace",
      "Ship/Rail",
      "Semiconductors & electronics",
      "Electrical equipment & appliances",
      "Machinery",
      "Pharma & medical devices",
      "Textiles/apparel/footwear",
      "Furniture",
      "Packaged foods & beverages",
      "CPG",
      "Printing",
    ],
    leadComplexity: "Multi-tier global supply; BOM/tooling qualification; long development in chips/aircraft/drugs.",
    dependencies: ["Qualified suppliers", "IP & tooling", "QA/QC & validation", "Skilled labor", "Automation", "JIT/JIC"],
    regulatory: [
      "Product safety & conformity (CE/UL/CCC)",
      "GMP/GDP & market authorization",
      "Export controls & sanctions",
      "IP & licensing",
      "Worker safety",
      "Conflict minerals",
    ],
  },
  "Market Access, Trading & Wholesale": {
    what: "Aggregate/disaggregate lots; risk/finance/title transfer.",
    industries: ["Commodity traders", "Wholesale distributors (general & specialty)", "B2B marketplaces", "Procurement consortia"],
    leadComplexity: "Contracting & hedging; seasonality; credit/counterparty risk; multi-Incoterm flows.",
    dependencies: ["Working capital", "Trade finance (LCs)", "Hedging (futures)", "Inventory visibility"],
    regulatory: ["Antitrust", "Anti-bribery", "Sanctions/KYC/AML", "Customs valuation", "Wholesale licenses", "Fair trading"],
  },
  "Logistics, Ports & Fulfillment": {
    what: "Move and store goods.",
    industries: [
      "Ocean/air/rail/road freight",
      "Pipelines",
      "Parcel/express",
      "Warehousing/DCs",
      "3PL/4PL",
      "Cold chain",
      "Customs brokerage",
      "Cross-border e-commerce logistics",
    ],
    leadComplexity: "Ocean weeks; air days; last-mile hours; capacity & reliability constraints.",
    dependencies: ["Ports/airports/roads", "Fleet & equipment", "WMS/TMS", "Data sharing", "Temperature control"],
    regulatory: ["Customs & trade facilitation", "Security (AEO, C-TPAT)", "Cabotage", "Driver hours & road safety", "Dangerous goods", "Biosecurity & SPS"],
  },
  "Retail & Direct-to-Consumer (Goods)": {
    what: "Sell physical goods to end customers.",
    industries: ["Grocery", "Department/hypermarkets", "Specialty retail", "Convenience", "Auto dealers", "Marketplaces", "DTC brands", "Vending"],
    leadComplexity: "Forecasting; assortment; planograms; returns; promotions; last-mile SLAs.",
    dependencies: ["POS data", "Omnichannel ops", "Reverse logistics", "Trust & brand"],
    regulatory: ["Consumer protection & labeling", "Pricing & promotion rules", "Product liability", "Data/privacy", "Accessibility"],
  },
  "Service Delivery (End-User Services)": {
    what: "Deliver experiences and intangible value.",
    industries: [
      "Healthcare & pharmacies",
      "Education",
      "Hospitality & food service",
      "Travel & passenger transport",
      "Financial services",
      "Media & entertainment",
      "Professional services",
      "Personal care",
      "Utilities retailing",
    ],
    leadComplexity: "Capacity planning; perishability of capacity; quality variance.",
    dependencies: ["Skilled workforce", "Facilities", "Digital platforms", "Physical inputs (drugs/food/spares)"],
    regulatory: ["Professional licensing", "Health & safety", "Tariff/fee schedules", "USOs", "Consumer protection", "Data sovereignty"],
  },
  "After-Sales, Reverse & End-of-Life": {
    what: "Keep assets running, recover value, dispose responsibly.",
    industries: [
      "Industrial/aviation/medical MRO",
      "Warranty & field service",
      "Spare parts",
      "Refurb/re-commerce",
      "Municipal waste",
      "E-waste & battery recycling",
      "Metal/paper/plastic recycling",
      "Hazardous waste",
      "Remanufacturing",
    ],
    leadComplexity: "Uncertain returns; long-tail spares; traceability; documentation.",
    dependencies: ["Tech data & parts", "Take-back schemes", "Secondary markets", "Certification"],
    regulatory: ["EPR", "WEEE/RoHS/REACH", "Landfill & incineration", "Right-to-repair", "Carbon & circularity reporting"],
  },
};

const CROSS_ENABLERS = [
  "Finance & Insurance",
  "Payments",
  "Standards & Certification",
  "Legal & Compliance",
  "Data/IT & Cybersecurity",
  "Telecom",
  "Energy & Utilities",
  "Workforce & Training",
  "R&D & Design",
  "ESG & Reporting",
] as const;

// -----------------------------
// 5-Tier Catalogs (expanded)
// -----------------------------
const VC: Record<string, string[]> = {
  Automotive: ["Vehicle Assembly", "Auto Parts", "EV Batteries"],
  Aerospace: ["Aircraft Assembly", "MRO", "Avionics"],
  Electronics: ["Semiconductors", "Consumer Devices", "Industrial IoT"],
  Agriculture: ["Row Crops", "Horticulture", "Livestock"],
  Fisheries: ["Aquaculture", "Wild Capture", "Cold Chain"],
  Mining: ["Open Pit", "Underground", "Mineral Processing"],
  "Oil & Gas": ["Upstream", "Midstream", "Downstream"],
  Chemicals: ["Basic Chemicals", "Specialty", "Fertilizers"],
  Pharmaceuticals: ["APIs", "Formulation", "Distribution"],
  "Textiles & Apparel": ["Spinning", "Weaving", "Garments"],
  "Food & Beverage": ["Meat Processing", "Dairy", "Beverages"],
  Construction: ["Cement", "Building Materials", "Contracting"],
  Utilities: ["Power Generation", "Transmission", "Distribution"],
  Logistics: ["Courier", "Freight Forwarding", "Warehousing", "Air Cargo"],
  "Wholesale & Trading": ["Commodity Trading", "Pharma Wholesale", "B2B Marketplace"],
  Retail: ["Grocery", "General Merchandise", "E-commerce"],
  Hospitality: ["Hotels", "Restaurants", "Catering"],
  "Travel & Tourism": ["Airlines", "Cruise", "Tour Operators"],
  Healthcare: ["Hospitals", "Clinics", "Pharmacies"],
  Education: ["K-12", "Universities", "Vocational"],
  RealEstate: ["Residential", "Commercial", "Property Mgmt"],
  Telecommunications: ["Mobile", "Fixed Broadband", "Data Centers"],
  ITServices: ["Software Dev", "Managed Services", "Cloud"],
  "Waste & Recycling": ["Solid Waste", "Recycling", "E-waste"],
  Metals: ["Steel", "Non-ferrous", "Foundry"],
  "Wood & Paper": ["Forestry", "Pulp", "Paper & Packaging"],
  Plastics: ["Resins", "Molding", "Recycling"],
  Maritime: ["Shipbuilding", "Ports", "Shipping Lines"],
  Furniture: ["Residential", "Office", "Fixtures"],
};

// Stage → applicable industries (for Stage-first selection)
const STAGE_TO_VC_MAP: Record<VCStageKey, (keyof typeof VC)[]> = {
  "Resource Extraction (Primary)": ["Mining", "Oil & Gas", "Agriculture", "Fisheries", "Wood & Paper"],
  "Primary Processing (Materials)": ["Chemicals", "Metals", "Construction", "Food & Beverage", "Wood & Paper", "Plastics", "Oil & Gas"],
  "Secondary Manufacturing & Assembly": [
    "Automotive",
    "Aerospace",
    "Electronics",
    "Pharmaceuticals",
    "Textiles & Apparel",
    "Furniture",
    "Plastics",
    "Metals",
    "Wood & Paper",
    "Food & Beverage",
  ],
  "Market Access, Trading & Wholesale": ["Wholesale & Trading", "Retail", "Food & Beverage", "Pharmaceuticals"],
  "Logistics, Ports & Fulfillment": ["Logistics", "Maritime", "Retail", "Agriculture", "Food & Beverage"],
  "Retail & Direct-to-Consumer (Goods)": ["Retail", "Hospitality", "Travel & Tourism"],
  "Service Delivery (End-User Services)": ["Healthcare", "Education", "Hospitality", "Travel & Tourism", "Utilities", "ITServices", "Telecommunications"],
  "After-Sales, Reverse & End-of-Life": ["Waste & Recycling", "Automotive", "Electronics", "Healthcare", "Logistics"],
};

const ENGINES: Engine[] = ["PostgreSQL", "MySQL", "SQL Server"];
const ENVS: Env[] = ["Dev", "UAT", "Prod"];
const SCOPES = [
  "Accounting",
  "Sales",
  "Inventory",
  "Manufacturing",
  "Procurement",
  "HRM",
  "Logistics",
  "Communications",
  "Compliance",
  "Analytics",
  "MediaSpecific",
  "FinanceSpecific",
] as const;

// -----------------------------
// Utilities
// -----------------------------
const pick = <T,>(arr: T[], i: number) => arr[i % arr.length];
const slug = (s: string) => s.toLowerCase().replace(/[^a-z0-9]+/g, "_").replace(/^_|_$/g, "");
function makeId(prefix: string, n: number) {
  return `${prefix}-${String(n).padStart(4, "0")}`;
}
function dateOffset(days: number) {
  const d = new Date();
  d.setDate(d.getDate() - days);
  return d.toISOString().slice(0, 10);
}

function extractStage(r: DbRecord): VCStageKey | null {
  if (r.tier !== "Value Chain") return null;
  const parts = r.path.split("→").map((s) => s.trim());
  // Expected: Value Chain → Stage → Industry → Subindustry
  if (parts.length >= 2) {
    const stage = parts[1] as VCStageKey;
    if ((VC_STACK as any)[stage]) return stage;
  }
  return null;
}

function scopesFromStage(stage?: VCStageKey): string[] {
  const base = new Set<string>();
  switch (stage) {
    case "Resource Extraction (Primary)":
      ["Procurement", "Logistics", "HRM", "Compliance", "Analytics"].forEach((s) => base.add(s));
      break;
    case "Primary Processing (Materials)":
      ["Manufacturing", "Inventory", "Compliance", "HRM", "Logistics", "Analytics"].forEach((s) => base.add(s));
      break;
    case "Secondary Manufacturing & Assembly":
      ["Manufacturing", "Procurement", "Inventory", "Compliance", "Analytics", "HRM", "Logistics"].forEach((s) => base.add(s));
      break;
    case "Market Access, Trading & Wholesale":
      ["Sales", "Inventory", "Accounting", "Compliance", "Analytics", "Logistics"].forEach((s) => base.add(s));
      break;
    case "Logistics, Ports & Fulfillment":
      ["Logistics", "Inventory", "Compliance", "Analytics"].forEach((s) => base.add(s));
      break;
    case "Retail & Direct-to-Consumer (Goods)":
      ["Sales", "Inventory", "Accounting", "Logistics", "Communications", "Analytics"].forEach((s) => base.add(s));
      break;
    case "Service Delivery (End-User Services)":
      ["HRM", "Communications", "Inventory", "Accounting", "Compliance", "Analytics"].forEach((s) => base.add(s));
      break;
    case "After-Sales, Reverse & End-of-Life":
      ["Logistics", "Inventory", "Compliance", "Analytics", "Communications"].forEach((s) => base.add(s));
      break;
    default:
      break;
  }
  return Array.from(base);
}

function scopesFromEnablers(enablers: readonly string[] = []) {
  const base = new Set<string>();
  enablers.forEach((e) => {
    if (e === "Finance & Insurance") {
      base.add("FinanceSpecific");
      base.add("Accounting");
    }
    if (e === "Payments") {
      base.add("FinanceSpecific");
      base.add("Sales");
    }
    if (e === "Standards & Certification") {
      base.add("Compliance");
    }
    if (e === "Legal & Compliance") {
      base.add("Compliance");
    }
    if (e === "Data/IT & Cybersecurity") {
      base.add("Analytics");
      base.add("Compliance");
    }
    if (e === "Telecom") {
      base.add("Communications");
    }
    if (e === "Energy & Utilities") {
      base.add("Manufacturing");
    }
    if (e === "Workforce & Training") {
      base.add("HRM");
    }
    if (e === "R&D & Design") {
      base.add("Manufacturing");
      base.add("Analytics");
    }
    if (e === "ESG & Reporting") {
      base.add("Compliance");
      base.add("Analytics");
    }
  });
  return Array.from(base);
}

function scopeSuggest(tier: Tier, sub?: string, stage?: VCStageKey, enablers: readonly string[] = []): string[] {
  const base = new Set<string>();
  if (tier === "Value Chain") {
    ["Accounting", "Inventory", "Manufacturing", "Logistics", "HRM", "Compliance", "Analytics"].forEach((s) => base.add(s));
    if (sub?.includes("Retail")) base.add("Sales");
  }
  if (tier === "Public Goods & Governance") ["Inventory", "Logistics", "Compliance", "Analytics", "Communications"].forEach((s) => base.add(s));
  if (tier === "CSO") ["Accounting", "HRM", "Communications", "Compliance", "Analytics"].forEach((s) => base.add(s));
  if (tier === "Media") ["MediaSpecific", "Sales", "Analytics", "Compliance", "Communications"].forEach((s) => base.add(s));
  if (tier === "Financial") ["FinanceSpecific", "Compliance", "Analytics", "Accounting", "Sales", "HRM"].forEach((s) => base.add(s));
  scopesFromStage(stage).forEach((s) => base.add(s));
  scopesFromEnablers(enablers).forEach((s) => base.add(s));
  return Array.from(base);
}

function buildPath(draft: NewDraft) {
  switch (draft.tier) {
    case "Value Chain":
      return `Value Chain → ${draft.vc_stage || "Stage"} → ${draft.industry || "Industry"} → ${draft.subindustry || "Subindustry"}`;
    case "Public Goods & Governance":
      return `Public Goods → ${draft.public_good || "PG"} → ${draft.lead_org || "Org"} → ${draft.program || "Program"}`;
    case "CSO":
      return `CSO → ${draft.cso_super || "Super"} → ${draft.cso_type || "Type"}`;
    case "Media":
      return `Media → ${draft.media_sector || "Sector"} → ${draft.media_subsector || "Subsector"} → ${draft.media_channel || "Channel"}`;
    case "Financial":
      return `Financial → ${draft.fin_sector || "Sector"} → ${draft.fin_subsector || "Subsector"} → ${draft.institution || "Institution"}`;
    default:
      return "Value Chain → Stage → Industry → Subindustry";
  }
}

function suggestName(d: NewDraft) {
  if (d.tier === "Value Chain" && d.vc_stage && d.industry && d.subindustry)
    return `${slug(d.vc_stage)}_${slug(d.industry)}_${slug(d.subindustry)}`;
  if (d.tier === "Public Goods & Governance" && d.public_good && d.program) return `${slug(d.public_good)}_${slug(d.program)}`;
  if (d.tier === "Media" && d.media_sector && d.media_subsector) return `${slug(d.media_sector)}_${slug(d.media_subsector)}`;
  if (d.tier === "Financial" && d.fin_sector && d.fin_subsector) return `${slug(d.fin_sector)}_${slug(d.fin_subsector)}`;
  if (d.tier === "CSO" && d.cso_super && d.cso_type) return `${slug(d.cso_super.split(" ")[0])}_${slug(d.cso_type)}`;
  return "new_database";
}

function generateDDL(engine: string | undefined, draft: NewDraft) {
  const schemas = ["acc_", "inv_", "mfg_", "hr_", "log_", "gov_", "media_", "fin_", "kpi_", "ref_"];
  const statements: string[] = [];
  const createSchema = (s: string) => {
    if (engine === "PostgreSQL") return `CREATE SCHEMA IF NOT EXISTS ${s};`;
    if (engine === "MySQL") return `-- MySQL: table names will be prefixed with ${s}`;
    if (engine === "SQL Server")
      return `IF NOT EXISTS (SELECT 1 FROM sys.schemas WHERE name = '${s}') EXEC('CREATE SCHEMA ${s}');`;
    return `-- Unknown engine`;
  };
  const createTable = (schema: string, name: string) => {
    if (engine === "PostgreSQL")
      return `CREATE TABLE IF NOT EXISTS ${schema}.${name} (id BIGSERIAL PRIMARY KEY, created_at TIMESTAMPTZ DEFAULT now());`;
    if (engine === "MySQL")
      return `CREATE TABLE IF NOT EXISTS ${schema}${name} (id BIGINT AUTO_INCREMENT PRIMARY KEY, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB;`;
    if (engine === "SQL Server")
      return `IF OBJECT_ID('${schema}.${name}', 'U') IS NULL CREATE TABLE ${schema}.${name} (id BIGINT IDENTITY(1,1) PRIMARY KEY, created_at DATETIME2 DEFAULT SYSUTCDATETIME());`;
    return `-- Unknown engine`;
  };
  schemas.forEach((s) => statements.push(createSchema(s)));
  const wants = new Set(draft.functional_scopes || []);
  if (wants.has("Accounting")) statements.push(createTable("acc_", "ledger"));
  if (wants.has("Inventory")) statements.push(createTable("inv_", "item"));
  if (wants.has("Manufacturing")) statements.push(createTable("mfg_", "work_order"));
  if (wants.has("HRM")) statements.push(createTable("hr_", "person"));
  if (wants.has("Logistics")) statements.push(createTable("log_", "shipment"));
  if (wants.has("Compliance")) statements.push(createTable("gov_", "obligation_evidence"));
  if (wants.has("MediaSpecific")) statements.push(createTable("media_", "platform_kpi"));
  if (wants.has("FinanceSpecific")) statements.push(createTable("fin_", "institution_license"));
  return statements.join("\n");
}

function downloadText(text: string, fileName: string) {
  const blob = new Blob([text], { type: "text/plain;charset=utf-8;" });
  const url = URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = fileName;
  a.click();
  URL.revokeObjectURL(url);
}

// -----------------------------
// Seed data (registry)
// -----------------------------
function buildInitialRegistry(): DbRecord[] {
  const out: DbRecord[] = [];
  let n = 1;
  const curated: DbRecord[] = [
    {
      id: makeId("vc", n++),
      name: "secondary_manufacturing_assembly_automotive_vehicle_assembly",
      engine: "PostgreSQL",
      environment: "UAT",
      tier: "Value Chain",
      path: "Value Chain → Secondary Manufacturing & Assembly → Automotive → Vehicle Assembly",
      scopes: scopeSuggest("Value Chain", "Vehicle Assembly", "Secondary Manufacturing & Assembly"),
      owner: "team-autos@example.com",
      status: "Healthy",
      createdAt: dateOffset(280),
      updatedAt: dateOffset(7),
    },
    {
      id: makeId("pg", n++),
      name: "health_expanded_immunization",
      engine: "PostgreSQL",
      environment: "Prod",
      tier: "Public Goods & Governance",
      path: "Public Goods → Health → Ministry of Health → Expanded Immunization",
      scopes: scopeSuggest("Public Goods & Governance"),
      owner: "moh@agency.gov",
      status: "Healthy",
      createdAt: dateOffset(420),
      updatedAt: dateOffset(3),
    },
    {
      id: makeId("fin", n++),
      name: "payments_e_money_wallets",
      engine: "SQL Server",
      environment: "Prod",
      tier: "Financial",
      path: "Financial → Payments → E-money/Wallets",
      scopes: scopeSuggest("Financial"),
      owner: "payments@finance.io",
      status: "Degraded",
      createdAt: dateOffset(210),
      updatedAt: dateOffset(2),
    },
    {
      id: makeId("med", n++),
      name: "broadcasting_free_to_air_tv",
      engine: "MySQL",
      environment: "UAT",
      tier: "Media",
      path: "Media → Broadcasting → Free-to-Air TV",
      scopes: scopeSuggest("Media"),
      owner: "broadcast@media.co",
      status: "Healthy",
      createdAt: dateOffset(160),
      updatedAt: dateOffset(5),
    },
    {
      id: makeId("cso", n++),
      name: "ngos_operational_service_ngo",
      engine: "PostgreSQL",
      environment: "Dev",
      tier: "CSO",
      path: "CSO → NGOs & INGOs → Operational Service NGO",
      scopes: scopeSuggest("CSO"),
      owner: "ops@cso.org",
      status: "Healthy",
      createdAt: dateOffset(120),
      updatedAt: dateOffset(10),
    },
  ];
  out.push(...curated);

  const vcStages = Object.keys(VC_STACK) as VCStageKey[];
  Object.entries(VC).forEach(([ind, subs], i) => {
    subs.forEach((sub, j) => {
      const stage = pick(vcStages, i + j);
      out.push({
        id: makeId("vc", n++),
        name: `${slug(stage)}_${slug(ind)}_${slug(sub)}`,
        engine: pick(["PostgreSQL", "MySQL", "SQL Server"], j) as Engine,
        environment: pick(["Dev", "UAT", "Prod"], i + j) as Env,
        tier: "Value Chain",
        path: `Value Chain → ${stage} → ${ind} → ${sub}`,
        scopes: scopeSuggest("Value Chain", sub, stage),
        owner: `${slug(ind)}@example.com`,
        status: pick(["Healthy", "Degraded"], j + 1) as any,
        createdAt: dateOffset(365 - ((i + j) % 300)),
        updatedAt: dateOffset(15 - ((i + j) % 12)),
      });
    });
  });

  return out;
}

// -----------------------------
// Create Wizard Types
// -----------------------------
interface NewDraft {
  tier?: Tier;
  // Value Chain (Stage-first)
  vc_stage?: VCStageKey;
  industry?: string;
  subindustry?: string;
  cross_enablers?: string[];
  // Public Goods
  public_good?: string;
  lead_org?: string;
  program?: string;
  // CSO
  cso_super?: string;
  cso_type?: string;
  // Media
  media_sector?: string;
  media_subsector?: string;
  media_channel?: string;
  // Financial
  fin_sector?: string;
  fin_subsector?: string;
  institution?: string;
  // Common
  functional_scopes: string[];
  name?: string;
  engine?: Engine;
  environment?: Env;
  owner?: string;
}

// Reusable: scope multi-select with checkboxes
function ScopeMultiselect({ value, onChange }: { value: string[]; onChange: (v: string[]) => void }) {
  const [open, setOpen] = useState(false);
  const [q, setQ] = useState("");
  const options = (SCOPES as readonly string[]).filter((s) => s.toLowerCase().includes(q.toLowerCase()));
  function toggle(s: string) {
    if (value.includes(s)) onChange(value.filter((v) => v !== s));
    else onChange([...value, s]);
  }
  return (
    <div className="relative">
      <Button variant="outline" className="w-full justify-start gap-2" type="button" onClick={() => setOpen((o) => !o)}>
        <Filter className="h-4 w-4" /> Scopes {value.length ? <Badge variant="secondary" className="ml-2">{value.length}</Badge> : null}
      </Button>
      {open && (
        <div className="absolute z-10 mt-2 w-[20rem] rounded-xl border bg-white shadow-sm">
          <div className="p-2 border-b">
            <Input placeholder="Search scopes…" value={q} onChange={(e) => setQ(e.target.value)} />
          </div>
          <ScrollArea className="h-56">
            <div className="p-2 grid grid-cols-2 gap-2">
              {options.map((s) => (
                <label key={s} className="flex items-center gap-2 rounded-lg border p-2 hover:bg-neutral-50 text-sm">
                  <Checkbox checked={value.includes(s)} onCheckedChange={() => toggle(s)} />
                  {s}
                </label>
              ))}
            </div>
          </ScrollArea>
          <div className="p-2 border-t flex justify-end gap-2">
            <Button size="sm" variant="ghost" onClick={() => onChange([])}>
              Clear
            </Button>
            <Button size="sm" onClick={() => setOpen(false)}>
              Close
            </Button>
          </div>
        </div>
      )}
    </div>
  );
}

// --- Stage-first components (Value-Chain) ---
function Step1ScopeVC({ draft, setDraft }: { draft: NewDraft; setDraft: (d: NewDraft) => void }) {
  const stageKeys = Object.keys(VC_STACK) as VCStageKey[];
  const selectedStage = draft.vc_stage;
  const industryOptions = React.useMemo(() => (selectedStage ? STAGE_TO_VC_MAP[selectedStage] : []), [selectedStage]);
  const subindustryOptions = React.useMemo(
    () => (draft.industry ? VC[draft.industry as keyof typeof VC] || [] : []),
    [draft.industry]
  );

  return (
    <div className="grid gap-4">
      <div className="grid md:grid-cols-3 gap-3">
        <div className="grid gap-2">
          <Label>Value-Chain Stage</Label>
          <Select
            value={draft.vc_stage}
            onValueChange={(v) => setDraft({ ...draft, vc_stage: v as VCStageKey, industry: undefined, subindustry: undefined })}
          >
            <SelectTrigger>
              <SelectValue placeholder="Pick a stage" />
            </SelectTrigger>
            <SelectContent>
              {stageKeys.map((s) => (
                <SelectItem key={s} value={s}>
                  {s}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
          <div className="text-[11px] text-neutral-500">
            Order: <b>Stage → Industry → Subindustry</b>.
          </div>
        </div>

        <div className="grid gap-2">
          <Label>Industry</Label>
          <Select value={draft.industry} onValueChange={(v) => setDraft({ ...draft, industry: v, subindustry: undefined })} disabled={!selectedStage}>
            <SelectTrigger>
              <SelectValue placeholder={selectedStage ? "Choose industry" : "Pick a stage first"} />
            </SelectTrigger>
            <SelectContent>
              {(industryOptions || []).map((ind) => (
                <SelectItem key={String(ind)} value={String(ind)}>
                  {ind}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
        </div>

        <div className="grid gap-2">
          <Label>Subindustry</Label>
          <Select value={draft.subindustry} onValueChange={(v) => setDraft({ ...draft, subindustry: v })} disabled={!draft.industry}>
            <SelectTrigger>
              <SelectValue placeholder={draft.industry ? "Choose subindustry" : "Pick an industry first"} />
            </SelectTrigger>
            <SelectContent>
              {subindustryOptions.map((s) => (
                <SelectItem key={s} value={s}>
                  {s}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
        </div>
      </div>

      <div className="grid gap-2">
        <Label>Cross-Cutting Enablers</Label>
        <div className="grid grid-cols-2 md:grid-cols-5 gap-2">
          {CROSS_ENABLERS.map((e) => (
            <label key={e} className="flex items-center gap-2 rounded-xl border p-2 hover:bg-neutral-50 text-xs">
              <Checkbox
                checked={draft.cross_enablers?.includes(e)}
                onCheckedChange={() => {
                  const cur = new Set(draft.cross_enablers || []);
                  cur.has(e) ? cur.delete(e) : cur.add(e);
                  const implied = scopeSuggest("Value Chain", draft.subindustry, draft.vc_stage, Array.from(cur));
                  const merged = Array.from(new Set([...(draft.functional_scopes || []), ...implied]));
                  setDraft({ ...draft, cross_enablers: Array.from(cur), functional_scopes: merged });
                }}
              />
              {e}
            </label>
          ))}
        </div>
      </div>

      {draft.vc_stage && (
        <Card className="mt-2">
          <CardContent className="p-4 text-xs">
            <div className="font-semibold mb-1">{draft.vc_stage}</div>
            <div className="mb-2 text-neutral-700">{VC_STACK[draft.vc_stage].what}</div>
            <div className="grid md:grid-cols-2 gap-2">
              <div>
                <span className="font-medium">Industries:</span> {VC_STACK[draft.vc_stage].industries.join(" • ")}
              </div>
              <div>
                <span className="font-medium">Lead times/complexity:</span> {VC_STACK[draft.vc_stage].leadComplexity}
              </div>
              <div>
                <span className="font-medium">Dependencies:</span> {VC_STACK[draft.vc_stage].dependencies.join(" • ")}
              </div>
              <div>
                <span className="font-medium">Regulatory touchpoints:</span> {VC_STACK[draft.vc_stage].regulatory.join(" • ")}
              </div>
            </div>
          </CardContent>
        </Card>
      )}

      <Card className="border-dashed">
        <CardContent className="p-4 text-xs text-neutral-600">
          Suggested scopes (stage + enablers):{" "}
          {scopeSuggest("Value Chain", draft.subindustry, draft.vc_stage, draft.cross_enablers || []).join(" • ")}
        </CardContent>
      </Card>
    </div>
  );
}

function Step2Scopes({ draft, setDraft }: { draft: NewDraft; setDraft: (d: NewDraft) => void }) {
  const [q, setQ] = useState("");
  const options = (SCOPES as readonly string[]).filter((s) => s.toLowerCase().includes(q.toLowerCase()));
  function toggle(scope: string) {
    const has = draft.functional_scopes.includes(scope);
    const next = has ? draft.functional_scopes.filter((s) => s !== scope) : [...draft.functional_scopes, scope];
    setDraft({ ...draft, functional_scopes: next });
  }
  const suggested = scopeSuggest(draft.tier || "Value Chain", draft.subindustry, draft.vc_stage, draft.cross_enablers || []);
  return (
    <div className="grid gap-3">
      <div className="flex items-center gap-2">
        <Input placeholder="Filter scopes…" value={q} onChange={(e) => setQ(e.target.value)} className="max-w-xs" />
        {suggested.length > 0 && <Badge variant="secondary" className="ml-auto">Suggested: {suggested.slice(0, 8).join(", ")}{suggested.length > 8 ? "…" : ""}</Badge>}
      </div>
      <div className="grid grid-cols-2 md:grid-cols-3 gap-2">
        {options.map((scope) => (
          <label key={scope} className="flex items-center gap-2 rounded-xl border p-2 hover:bg-neutral-50 text-sm">
            <Checkbox checked={draft.functional_scopes.includes(scope)} onCheckedChange={() => toggle(scope)} />
            {scope}
          </label>
        ))}
      </div>
    </div>
  );
}

function Step3MetaVC({
  draft,
  setDraft,
  onDownload,
  onGenerate,
}: {
  draft: NewDraft;
  setDraft: (d: NewDraft) => void;
  onDownload: () => void;
  onGenerate: () => void;
}) {
  const ddl = generateDDL(draft.engine, draft);
  return (
    <div className="grid gap-4">
      <div className="grid md:grid-cols-2 gap-3">
        <div className="grid gap-2">
          <Label>Database/App Name</Label>
          <Input placeholder={suggestName(draft)} value={draft.name || ""} onChange={(e) => setDraft({ ...draft, name: e.target.value })} />
          <div className="text-[11px] text-neutral-500">Path: {buildPath(draft)}</div>
        </div>
        <div className="grid gap-2">
          <Label>Owner (email)</Label>
          <Input placeholder="owner@example.com" value={draft.owner || ""} onChange={(e) => setDraft({ ...draft, owner: e.target.value })} />
        </div>
      </div>
      <div className="grid md:grid-cols-3 gap-3">
        <div className="grid gap-2">
          <Label>Engine</Label>
          <Select value={draft.engine} onValueChange={(v) => setDraft({ ...draft, engine: v as Engine })}>
            <SelectTrigger>
              <SelectValue placeholder="Select" />
            </SelectTrigger>
            <SelectContent>
              {["PostgreSQL", "MySQL", "SQL Server"].map((e) => (
                <SelectItem key={e} value={e}>
                  {e}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
        </div>
        <div className="grid gap-2">
          <Label>Environment</Label>
          <Select value={draft.environment} onValueChange={(v) => setDraft({ ...draft, environment: v as Env })}>
            <SelectTrigger>
              <SelectValue placeholder="Select" />
            </SelectTrigger>
            <SelectContent>
              {["Dev", "UAT", "Prod"].map((e) => (
                <SelectItem key={e} value={e}>
                  {e}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
        </div>
        <div className="grid gap-2">
          <Label>Suggested Name</Label>
          <Input value={suggestName(draft)} readOnly />
        </div>
      </div>
      <Card>
        <CardHeader>
          <CardTitle className="text-sm">DDL Preview ({draft.engine})</CardTitle>
        </CardHeader>
        <CardContent>
          <ScrollArea className="h-56 rounded-xl border p-3 text-xs font-mono">
            <pre>{ddl}</pre>
          </ScrollArea>
          <div className="mt-3 flex items-center justify-between">
            <div className="text-xs text-neutral-500">Schemas: acc_, inv_, mfg_, hr_, log_, gov_, media_, fin_, kpi_, ref_</div>
            <div className="flex items-center gap-2">
              <Button variant="outline" size="sm" onClick={() => navigator.clipboard?.writeText(ddl)}>
                Copy
              </Button>
              <Button variant="outline" size="sm" onClick={onDownload}>
                Download .sql
              </Button>
              <Button size="sm" className="gap-1" onClick={onGenerate}>
                Generate <ChevronRight className="h-4 w-4" />
              </Button>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}

// Guide Panel (tiers + stage stack)
function GuidePanel() {
  const stages = Object.keys(VC_STACK) as VCStageKey[];
  return (
    <Card>
      <CardHeader>
        <CardTitle className="text-base">Five-Tier Reference, Value-Chain Stage Stack & Functional Mapping</CardTitle>
      </CardHeader>
      <CardContent>
        <ScrollArea className="h-[560px]">
          <div className="pr-4 grid gap-6 text-sm">
            <section>
              <h3 className="font-semibold mb-1">Value-Chain Stack (Goods & Services)</h3>
              <p className="text-neutral-600 mb-2">
                Selection order is <b>Stage → Industry → Subindustry</b>. Stages span end-to-end—from extraction to end-of-life—and combine with
                cross-cutting enablers (finance, payments, standards, IT/security, telecom, energy, workforce, R&D/design, ESG/reporting).
              </p>
              <div className="grid md:grid-cols-2 gap-3">
                {stages.map((s) => (
                  <GuideCard
                    key={s}
                    title={s}
                    common={[`What: ${VC_STACK[s].what}`, `Lead/complexity: ${VC_STACK[s].leadComplexity}`]}
                    specific={[
                      `Industries: ${VC_STACK[s].industries.join(" • ")}`,
                      `Dependencies: ${VC_STACK[s].dependencies.join(" • ")}`,
                      `Regulatory: ${VC_STACK[s].regulatory.join(" • ")}`,
                    ]}
                  />
                ))}
              </div>
              <div className="mt-2 text-xs">
                <span className="font-medium">Cross-cutting enablers:</span> {Array.from(CROSS_ENABLERS).join(" • ")}
              </div>
            </section>

            <section>
              <h3 className="font-semibold mb-1">Five Tiers</h3>
              <p className="text-neutral-600 mb-2">Tiers organize the ecosystem. Each suggests common modules and adds entity-specific functions.</p>
              <div className="grid md:grid-cols-2 gap-3">
                <GuideCard
                  title="1) Value Chain"
                  common={["Accounting", "Inventory", "Manufacturing", "Procurement", "Logistics", "HRM", "Compliance", "Analytics"]}
                  specific={["Stage-driven scopes (e.g., Retail ⇒ Sales/Inventory)", "Enabler-driven scopes (e.g., Payments ⇒ FinanceSpecific)"]}
                />
                <GuideCard
                  title="2) Public Goods & Governance"
                  common={["Beneficiary Registry", "Program Mgmt", "Inventory & Logistics", "Payments/Payroll", "Compliance", "Analytics", "Communications"]}
                  specific={["Cold chain (Health)", "Timetables & PSO (Transport)", "Tariffs & billing (Utilities)"]}
                />
                <GuideCard
                  title="3) CSO/Community"
                  common={["Membership", "Donations/Grants", "Projects", "HR/Volunteers", "Communications", "Compliance"]}
                  specific={["CBA tracking (Unions)", "By-laws/Voting (HOA)"]}
                />
                <GuideCard
                  title="4) Media"
                  common={["Content Catalog", "Scheduling/Playout", "Ad Sales/Traffic", "Audience Analytics", "Compliance/Ratings"]}
                  specific={["DRM/Subscription (OTT)", "Regulatory Logs (Broadcast)"]}
                />
                <GuideCard
                  title="5) Financial"
                  common={["GL", "KYC/AML", "Payments", "Risk", "Analytics"]}
                  specific={["Wallet Settlement (Payments)", "Claims/Reinsurance (Insurance)", "OMS/Clearing (Capital Markets)"]}
                />
              </div>
            </section>
          </div>
        </ScrollArea>
      </CardContent>
    </Card>
  );
}

function GuideCard({ title, common, specific }: { title: string; common: string[]; specific: string[] }) {
  return (
    <Card>
      <CardHeader>
        <CardTitle className="text-sm">{title}</CardTitle>
      </CardHeader>
      <CardContent>
        <div className="text-xs">
          <span className="font-medium">Common:</span> {common.join(" • ")}
        </div>
        <div className="text-xs mt-1">
          <span className="font-medium">Entity-specific:</span> {specific.join(" • ")}
        </div>
      </CardContent>
    </Card>
  );
}

// Command Palette & Help
function CommandPalette({ open, onOpenChange, onGo }: { open: boolean; onOpenChange: (v: boolean) => void; onGo: (tab: string) => void }) {
  return (
    <CommandDialog open={open} onOpenChange={onOpenChange}>
      <CommandInput placeholder="Type a command or search…" />
      <CommandList>
        <CommandEmpty>No results.</CommandEmpty>
        <CommandGroup heading="Navigate">
          <CommandItem onSelect={() => onGo("registry")}>Open Registry</CommandItem>
          <CommandItem onSelect={() => onGo("create")}>Open Create</CommandItem>
          <CommandItem onSelect={() => onGo("guide")}>Open Guide</CommandItem>
        </CommandGroup>
        <CommandSeparator />
        <CommandGroup heading="Actions">
          <CommandItem onSelect={() => onGo("export")}>Export Registry CSV</CommandItem>
        </CommandGroup>
      </CommandList>
    </CommandDialog>
  );
}

function HelpSheet({ open, onOpenChange }: { open: boolean; onOpenChange: (v: boolean) => void }) {
  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="max-w-xl">
        <DialogHeader>
          <DialogTitle>Help & Tips</DialogTitle>
          <DialogDescription>Quick shortcuts and usage guidance</DialogDescription>
        </DialogHeader>
        <div className="text-sm text-neutral-700 space-y-2">
          <p>
            Use the <b>Value-Chain Create</b> wizard to generate stage-aligned apps. Selection order is <b>Stage → Industry → Subindustry</b>.
            Enablers influence suggested functional scopes and DDL.
          </p>
          <ul className="list-disc pl-5 text-xs">
            <li>
              <kbd className="px-1 border rounded">/</kbd> search; <kbd className="px-1 border rounded">Cmd/Ctrl+K</kbd> commands;{" "}
              <kbd className="px-1 border rounded">?</kbd> help
            </li>
            <li>Saved views help you switch quickly between common filter sets.</li>
            <li>Click any row to open Quick View and copy its path, owner, or scopes.</li>
          </ul>
        </div>
      </DialogContent>
    </Dialog>
  );
}

function QuickViewDialog({ row, onClose }: { row: DbRecord | null; onClose: () => void }) {
  return (
    <Dialog open={!!row} onOpenChange={(v) => !v && onClose()}>
      <DialogContent className="max-w-2xl">
        <DialogHeader>
          <DialogTitle className="flex items-center gap-2">
            <Eye className="h-4 w-4" />
            {row?.name}
          </DialogTitle>
          <DialogDescription>
            Engine: {row?.engine} • Env: {row?.environment}
          </DialogDescription>
        </DialogHeader>
        {row && (
          <div className="text-sm grid gap-2">
            <div>
              <span className="font-medium">Path:</span> <span className="text-neutral-600">{row.path}</span>
            </div>
            <div>
              <span className="font-medium">Tier:</span> {row.tier} {extractStage(row) ? <span className="text-xs text-neutral-500">(Stage: {extractStage(row)})</span> : null}
            </div>
            <div>
              <span className="font-medium">Owner:</span> {row.owner}
            </div>
            <div className="flex flex-wrap gap-1">
              <span className="font-medium mr-1">Scopes:</span> {row.scopes.map((s) => <Badge key={s} variant="outline">{s}</Badge>)}
            </div>
            <div className="text-xs text-neutral-500">
              Created: {row.createdAt} • Updated: {row.updatedAt}
            </div>
          </div>
        )}
      </DialogContent>
    </Dialog>
  );
}

// -----------------------------
// Main Component (Tabbed)
// -----------------------------
export default function CoreDatabasesWorkbench() {
  // Global UI state
  const [activeTab, setActiveTab] = useState("registry");
  const [compact, setCompact] = useState(false);
  const [commandOpen, setCommandOpen] = useState(false);
  const [helpOpen, setHelpOpen] = useState(false);

  // Registry state
  const [registry, setRegistry] = useState<DbRecord[]>(() => buildInitialRegistry());

  // Filters
  const [q, setQ] = useState("");
  const [tier, setTier] = useState<Tier | "All">("All");
  const [vcStageFilter, setVcStageFilter] = useState<VCStageKey | "All">("All");
  const [engine, setEngine] = useState<Engine | "All">("All");
  const [env, setEnv] = useState<Env | "All">("All");
  const [scopeFilter, setScopeFilter] = useState<string[]>([]);
  const [sortBy, setSortBy] = useState<keyof DbRecord>("name");
  const [sortDir, setSortDir] = useState<"asc" | "desc">("asc");
  const [page, setPage] = useState(1);
  const pageSize = 20;

  const [quickView, setQuickView] = useState<DbRecord | null>(null);

  useEffect(() => {
    const onKey = (e: KeyboardEvent) => {
      if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === "k") {
        e.preventDefault();
        setCommandOpen(true);
      }
      if (e.key === "/") {
        const el = document.getElementById("global-search");
        if (el) {
          e.preventDefault();
          (el as HTMLInputElement).focus();
        }
      }
      if (e.key === "?" && !commandOpen) {
        e.preventDefault();
        setHelpOpen(true);
      }
      if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === "g") {
        setActiveTab("guide");
      }
    };
    window.addEventListener("keydown", onKey);
    return () => window.removeEventListener("keydown", onKey);
  }, [commandOpen]);

  const filtered = useMemo(() => {
    const text = q.trim().toLowerCase();
    let rows = registry.filter((r) => {
      if (tier !== "All" && r.tier !== tier) return false;
      if (engine !== "All" && r.engine !== engine) return false;
      if (env !== "All" && r.environment !== env) return false;
      if (scopeFilter.length && !scopeFilter.every((s) => r.scopes.includes(s))) return false;
      if (vcStageFilter !== "All") {
        const stage = extractStage(r);
        if (stage !== vcStageFilter) return false;
      }
      if (text) {
        const hay = `${r.name} ${r.path} ${r.owner} ${r.engine} ${r.environment} ${r.tier} ${r.scopes.join(" ")}`.toLowerCase();
        if (!hay.includes(text)) return false;
      }
      return true;
    });
    rows = rows.sort((a, b) => {
      const av = String(a[sortBy] ?? "").toLowerCase();
      const bv = String(b[sortBy] ?? "").toLowerCase();
      if (av < bv) return sortDir === "asc" ? -1 : 1;
      if (av > bv) return sortDir === "asc" ? 1 : -1;
      return 0;
    });
    return rows;
  }, [registry, tier, engine, env, scopeFilter, q, sortBy, sortDir, vcStageFilter]);

  const totalPages = Math.max(1, Math.ceil(filtered.length / pageSize));
  const pageRows = filtered.slice((page - 1) * pageSize, page * pageSize);

  function toCSV(rows: DbRecord[]): string {
    const headers = ["id", "name", "engine", "environment", "tier", "path", "scopes", "owner", "status", "createdAt", "updatedAt"];
    const escape = (v: any) => `"${String(v).replace(/"/g, '""')}"`;
    const lines = [headers.join(",")];
    rows.forEach((r) => {
      lines.push([r.id, r.name, r.engine, r.environment, r.tier, r.path, r.scopes.join(";"), r.owner, r.status, r.createdAt, r.updatedAt].map(escape).join(","));
    });
    return lines.join("\n");
  }
  function downloadCSV(rows: DbRecord[], fileName: string) {
    downloadText(toCSV(rows), fileName);
  }

  // Create tab state (Value-Chain tier by default)
  const [step, setStep] = useState(1);
  const [draft, setDraft] = useState<NewDraft>({
    functional_scopes: ["Accounting", "Inventory", "Analytics"],
    engine: "PostgreSQL",
    environment: "Dev",
    tier: "Value Chain",
  });

  function resetDraft() {
    setStep(1);
    setDraft({ functional_scopes: ["Accounting", "Inventory", "Analytics"], engine: "PostgreSQL", environment: "Dev", tier: "Value Chain" });
  }

  function createFromDraft() {
    const id = `gen-${Math.random().toString(36).slice(2, 8)}`;
    const rec: DbRecord = {
      id,
      name: draft.name || suggestName(draft),
      engine: draft.engine || "PostgreSQL",
      environment: draft.environment || "Dev",
      tier: (draft.tier || "Value Chain") as Tier,
      path: buildPath(draft),
      scopes: scopeSuggest(draft.tier || "Value Chain", draft.subindustry, draft.vc_stage, draft.cross_enablers || [])
        .concat(draft.functional_scopes)
        .filter((v, i, a) => a.indexOf(v) === i),
      owner: draft.owner || "owner@example.com",
      status: "Healthy",
      createdAt: new Date().toISOString().slice(0, 10),
      updatedAt: new Date().toISOString().slice(0, 10),
    };
    setRegistry((prev) => [rec, ...prev]);
    setActiveTab("registry");
    resetDraft();
  }

  // Saved Views (examples)
  const savedViews: { name: string; apply: () => void }[] = [
    { name: "All Prod", apply: () => { setTier("All"); setEngine("All"); setEnv("Prod"); setVcStageFilter("All"); setScopeFilter([]); } },
    { name: "Stage: Logistics", apply: () => { setTier("Value Chain"); setVcStageFilter("Logistics, Ports & Fulfillment"); setEngine("All"); setEnv("All"); setScopeFilter([]); } },
    { name: "Public Goods", apply: () => { setTier("Public Goods & Governance"); setEngine("All"); setEnv("All"); setVcStageFilter("All"); setScopeFilter([]); } },
  ];

  return (
    <TooltipProvider>
      <Card className="shadow-sm">
        <CardHeader className="pb-3">
          <div className="flex items-center justify-between">
            <CardTitle className="text-base flex items-center gap-2">
              <Sparkles className="h-4 w-4" /> Core Databases Workbench
            </CardTitle>
            <div className="flex items-center gap-2">
              <Tooltip>
                <TooltipTrigger asChild>
                  <Button variant="outline" size="sm" onClick={() => setCommandOpen(true)} className="gap-2">
                    <Keyboard className="h-4 w-4" /> Cmd
                  </Button>
                </TooltipTrigger>
                <TooltipContent>Open Command Palette (Cmd/Ctrl+K)</TooltipContent>
              </Tooltip>
              <Tooltip>
                <TooltipTrigger asChild>
                  <Button variant="outline" size="sm" onClick={() => setHelpOpen(true)} className="gap-2">
                    <HelpCircle className="h-4 w-4" /> Help
                  </Button>
                </TooltipTrigger>
                <TooltipContent>Shortcuts & tips</TooltipContent>
              </Tooltip>
              <div className="flex items-center gap-2 border rounded-xl px-3 py-1.5 text-xs">
                <span>Compact</span>
                <Switch checked={compact} onCheckedChange={(v) => setCompact(!!v)} />
              </div>
            </div>
          </div>

          {/* Global toolbar */}
          <div className="mt-3 grid grid-cols-1 lg:grid-cols-12 gap-2">
            <div className="lg:col-span-4 flex items-center gap-2">
              <Search className="h-4 w-4 text-neutral-500" />
              <Input id="global-search" placeholder="Search name, path, owner… (press / to focus)" value={q} onChange={(e) => setQ(e.target.value)} />
            </div>
            <div className="lg:col-span-8 grid grid-cols-2 md:grid-cols-6 gap-2">
              <Select value={tier} onValueChange={(v) => setTier(v as any)}>
                <SelectTrigger>
                  <SelectValue placeholder="Tier" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="All">All Tiers</SelectItem>
                  {(["Value Chain", "Public Goods & Governance", "CSO", "Media", "Financial"] as Tier[]).map((t) => (
                    <SelectItem key={t} value={t}>
                      {t}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
              <Select value={vcStageFilter} onValueChange={(v) => setVcStageFilter(v as any)}>
                <SelectTrigger>
                  <SelectValue placeholder="Stage" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="All">All Stages</SelectItem>
                  {(Object.keys(VC_STACK) as VCStageKey[]).map((s) => (
                    <SelectItem key={s} value={s}>
                      {s}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
              <Select value={engine} onValueChange={(v) => setEngine(v as any)}>
                <SelectTrigger>
                  <SelectValue placeholder="Engine" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="All">All Engines</SelectItem>
                  {(["PostgreSQL", "MySQL", "SQL Server"] as Engine[]).map((e) => (
                    <SelectItem key={e} value={e}>
                      {e}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
              <Select value={env} onValueChange={(v) => setEnv(v as any)}>
                <SelectTrigger>
                  <SelectValue placeholder="Environment" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="All">All Envs</SelectItem>
                  {(["Dev", "UAT", "Prod"] as Env[]).map((e) => (
                    <SelectItem key={e} value={e}>
                      {e}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
              <ScopeMultiselect value={scopeFilter} onChange={setScopeFilter} />
              <Select onValueChange={(v) => savedViews.find((sv) => sv.name === v)?.apply()}>
                <SelectTrigger>
                  <SelectValue placeholder="Saved Views" />
                </SelectTrigger>
                <SelectContent>
                  {savedViews.map((sv) => (
                    <SelectItem key={sv.name} value={sv.name}>
                      {sv.name}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
          </div>
        </CardHeader>

        <CardContent className="pt-0">
          <Tabs value={activeTab} onValueChange={setActiveTab} className="w-full">
            <TabsList className="w-full justify-start gap-2 rounded-t-2xl border bg-white sticky top-0">
              <TabsTrigger value="registry" className="gap-2">
                <Database className="h-4 w-4" /> Registry
              </TabsTrigger>
              <TabsTrigger value="create" className="gap-2">
                <FilePlus2 className="h-4 w-4" /> Create
              </TabsTrigger>
              <TabsTrigger value="guide" className="gap-2">
                <BookOpenText className="h-4 w-4" /> Guide
              </TabsTrigger>
            </TabsList>

            {/* TAB 1: REGISTRY */}
            <TabsContent value="registry" className="mt-4">
              <div className="grid gap-3">
                {/* Stats bar */}
                <div className="flex flex-wrap items-center gap-2 text-xs">
                  <Badge variant="secondary">{filtered.length} databases</Badge>
                  <Separator orientation="vertical" className="h-4" />
                  <span className="text-neutral-500">Sort by</span>
                  <Select value={sortBy} onValueChange={(v) => setSortBy(v as keyof DbRecord)}>
                    <SelectTrigger className="w-40">
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      {(["name", "engine", "environment", "tier", "owner", "status", "updatedAt"] as (keyof DbRecord)[]).map((k) => (
                        <SelectItem key={k} value={k}>
                          {k}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                  <Select value={sortDir} onValueChange={(v) => setSortDir(v as any)}>
                    <SelectTrigger className="w-28">
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="asc">asc</SelectItem>
                      <SelectItem value="desc">desc</SelectItem>
                    </SelectContent>
                  </Select>
                  <Button size="sm" variant="outline" className="ml-auto gap-2" onClick={() => downloadCSV(filtered, "core_databases_registry.csv")}>
                    <Download className="h-4 w-4" /> Export CSV
                  </Button>
                </div>

                {/* Registry table */}
                <div className={`rounded-xl border overflow-hidden ${compact ? "text-xs" : ""}`}>
                  <Table>
                    <TableHeader>
                      <TableRow>
                        <TableHead>ID</TableHead>
                        <TableHead>Name</TableHead>
                        <TableHead>Engine</TableHead>
                        <TableHead>Env</TableHead>
                        <TableHead>Tier</TableHead>
                        <TableHead>Path</TableHead>
                        <TableHead>Scopes</TableHead>
                        <TableHead>Owner</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead>Updated</TableHead>
                      </TableRow>
                    </TableHeader>
                    <TableBody>
                      {pageRows.length === 0 && (
                        <TableRow>
                          <TableCell colSpan={10}>
                            <div className="py-10 text-center text-neutral-500">
                              <div className="font-medium">No results match your filters.</div>
                              <div className="text-xs">Try clearing scopes or stage, or switch to another Saved View.</div>
                            </div>
                          </TableCell>
                        </TableRow>
                      )}
                      {pageRows.map((r) => (
                        <TableRow key={r.id} className="hover:bg-neutral-50 cursor-pointer" onClick={() => setQuickView(r)}>
                          <TableCell className="text-xs">{r.id}</TableCell>
                          <TableCell className="font-medium">{r.name}</TableCell>
                          <TableCell>{r.engine}</TableCell>
                          <TableCell>
                            <Badge variant="secondary">{r.environment}</Badge>
                          </TableCell>
                          <TableCell>{r.tier}</TableCell>
                          <TableCell className="text-xs text-neutral-600">{r.path}</TableCell>
                          <TableCell>
                            <div className="flex flex-wrap gap-1">
                              {r.scopes.slice(0, 3).map((s) => (
                                <Badge key={s} variant="outline">
                                  {s}
                                </Badge>
                              ))}
                              {r.scopes.length > 3 && <Badge variant="outline">+{r.scopes.length - 3}</Badge>}
                            </div>
                          </TableCell>
                          <TableCell className="text-xs">{r.owner}</TableCell>
                          <TableCell>{r.status === "Healthy" ? <Badge className="bg-emerald-600">Healthy</Badge> : <Badge className="bg-amber-500">Degraded</Badge>}</TableCell>
                          <TableCell className="text-xs">{r.updatedAt}</TableCell>
                        </TableRow>
                      ))}
                    </TableBody>
                  </Table>
                </div>

                {/* Pagination */}
                <div className="flex items-center justify-between text-xs mt-2">
                  <div>
                    Page {page} of {totalPages}
                  </div>
                  <div className="flex items-center gap-2">
                    <Button size="sm" variant="outline" onClick={() => setPage((p) => Math.max(1, p - 1))} disabled={page === 1}>
                      Prev
                    </Button>
                    <Button size="sm" variant="outline" onClick={() => setPage((p) => Math.min(totalPages, p + 1))} disabled={page === totalPages}>
                      Next
                    </Button>
                  </div>
                </div>
              </div>
            </TabsContent>

            {/* TAB 2: CREATE */}
            <TabsContent value="create" className="mt-4">
              <div className="grid gap-4">
                <Card className="border-dashed">
                  <CardContent className="p-4 text-xs text-neutral-600 flex items-start gap-2">
                    <Info className="h-4 w-4 mt-0.5" />
                    <div>
                      For the Value-Chain tier, selection order is <strong>Stage → Industry → Subindustry</strong>. Enablers and stage refine
                      suggested functional scopes and the DDL preview. Use <kbd className="px-1 border rounded">Cmd/Ctrl+K</kbd> for quick actions.
                    </div>
                  </CardContent>
                </Card>

                {/* Stepper */}
                <div className="grid grid-cols-3 gap-2">
                  {[1, 2, 3].map((n) => (
                    <div
                      key={n}
                      className={`rounded-2xl border p-3 ${n === step ? "border-neutral-900 bg-white" : "bg-neutral-50"}`}
                      onClick={() => setStep(n)}
                    >
                      <div className="flex items-center gap-2 text-sm">
                        {n < step ? (
                          <CheckCircle2 className="h-4 w-4 text-emerald-600" />
                        ) : (
                          <span className="inline-flex h-5 w-5 items-center justify-center rounded-full border text-xs">{n}</span>
                        )}
                        <span className="font-medium">{n === 1 ? "Pick Stage & Scope" : n === 2 ? "Functional Scopes" : "Name & Engine"}</span>
                      </div>
                    </div>
                  ))}
                </div>

                {step === 1 && <Step1ScopeVC draft={draft} setDraft={setDraft} />}
                {step === 2 && <Step2Scopes draft={draft} setDraft={setDraft} />}
                {step === 3 && (
                  <Step3MetaVC
                    draft={draft}
                    setDraft={setDraft}
                    onDownload={() => downloadText(generateDDL(draft.engine, draft), `${suggestName(draft)}_${draft.engine}.sql`)}
                    onGenerate={createFromDraft}
                  />
                )}
              </div>
            </TabsContent>

            {/* TAB 3: GUIDE */}
            <TabsContent value="guide" className="mt-4">
              <GuidePanel />
            </TabsContent>
          </Tabs>
        </CardContent>
      </Card>

      {/* Overlays */}
      <CommandPalette
        open={commandOpen}
        onOpenChange={setCommandOpen}
        onGo={(tab) => {
          if (tab === "export") downloadCSV(filtered, "core_databases_registry.csv");
          else setActiveTab(tab);
          setCommandOpen(false);
        }}
      />
      <HelpSheet open={helpOpen} onOpenChange={setHelpOpen} />
      <QuickViewDialog row={quickView} onClose={() => setQuickView(null)} />
    </TooltipProvider>
  );
}
