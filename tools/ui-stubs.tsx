// @ts-nocheck
import React from "react";

// Generic box wrapper
const Box: React.FC<React.HTMLAttributes<HTMLDivElement>> = ({ children, className = "", ...rest }) => (
  <div className={className} {...rest}>{children}</div>
);

// Card family
export const Card = Box;
export const CardHeader = Box;
export const CardTitle: React.FC<React.HTMLAttributes<HTMLDivElement>> = ({ children, className = "font-semibold", ...rest }) => (
  <div className={className} {...rest}>{children}</div>
);
export const CardContent = Box;

// Basic inputs
export const Button: React.FC<React.ButtonHTMLAttributes<HTMLButtonElement>> = ({ children, className = "border rounded px-2 py-1", ...rest }) => (
  <button className={className} {...rest}>{children}</button>
);
export const Badge: React.FC<React.HTMLAttributes<HTMLSpanElement>> = ({ children, className = "inline-block border rounded px-2 py-0.5 text-xs", ...rest }) => (
  <span className={className} {...rest}>{children}</span>
);
export const Input: React.FC<React.InputHTMLAttributes<HTMLInputElement>> = ({ className = "border rounded px-2 py-1 w-full", ...rest }) => (
  <input className={className} {...rest} />
);
export const Label: React.FC<React.LabelHTMLAttributes<HTMLLabelElement>> = ({ className = "text-sm font-medium", ...rest }) => (
  <label className={className} {...rest} />
);

// Simplified Select primitives (wrapper around native select)
type SelectProps = { value?: string; onValueChange?: (v: string) => void; children?: React.ReactNode };
export const Select: React.FC<SelectProps> = ({ value, onValueChange, children }) => {
  // Extract SelectItem children
  const items: Array<{ value: string; label: string }> = [];
  React.Children.forEach(children as any, (child: any) => {
    if (!child) return;
    const content = child.props?.children;
    // look into nested SelectContent → SelectItem
    if (child.type?.displayName === "SelectContent") {
      React.Children.forEach(content, (sub: any) => {
        if (sub?.type?.displayName === "SelectItem") items.push({ value: sub.props.value, label: sub.props.children });
      });
    }
    if (child.type?.displayName === "SelectItem") items.push({ value: child.props.value, label: child.props.children });
  });
  return (
    <select value={value} onChange={(e) => onValueChange?.(e.target.value)} className="border rounded px-2 py-1">
      {items.map((it) => (
        <option key={it.value} value={it.value}>{it.label}</option>
      ))}
    </select>
  );
};
export const SelectTrigger: React.FC<React.HTMLAttributes<HTMLDivElement>> = (p) => <div {...p} />;
export const SelectValue: React.FC<{ placeholder?: string }> = ({ placeholder }) => <span>{placeholder ?? ""}</span>;
export const SelectContent: React.FC<React.HTMLAttributes<HTMLDivElement>> = ({ children, ...rest }) => <div {...rest}>{children}</div>;
SelectContent.displayName = "SelectContent";
export const SelectItem: React.FC<{ value: string; children: React.ReactNode }> = ({ children }) => <div>{children}</div>;
SelectItem.displayName = "SelectItem";

// Tabs (basic)
export const Tabs: React.FC<{ value: string; onValueChange: (v: string) => void; className?: string; children?: React.ReactNode }>
  = ({ value, onValueChange, className, children }) => <div className={className}>{children}</div>;
export const TabsList = Box;
export const TabsTrigger: React.FC<{ value: string; className?: string; children?: React.ReactNode }>
  = ({ value, className = "border rounded px-2 py-1", children }) => <button className={className} data-value={value}>{children}</button>;
export const TabsContent: React.FC<{ value: string; className?: string; children?: React.ReactNode }> = ({ className = "", children }) => <div className={className}>{children}</div>;

// Table (very light wrappers)
export const Table = Box; export const TableHeader = Box; export const TableBody = Box; export const TableRow = Box; export const TableHead = Box; export const TableCell = Box;

// Checkbox
export const Checkbox: React.FC<{ checked?: boolean; onCheckedChange?: (v: boolean) => void }>
  = ({ checked, onCheckedChange }) => <input type="checkbox" checked={!!checked} onChange={(e) => onCheckedChange?.(e.target.checked)} />;

export const ScrollArea = Box;
export const Separator: React.FC<{ orientation?: "horizontal" | "vertical"; className?: string }> = ({ orientation = "horizontal", className = "" }) => (
  <div className={className} style={{ width: orientation === "vertical" ? 1 : "100%", height: orientation === "vertical" ? "1.25rem" : 1, background: "#e5e7eb" }} />
);
export const Switch: React.FC<{ checked?: boolean; onCheckedChange?: (v: boolean) => void }>
  = ({ checked, onCheckedChange }) => <input type="checkbox" role="switch" checked={!!checked} onChange={(e) => onCheckedChange?.(e.target.checked)} />;

// Command palette stubs
export const CommandDialog = Box; export const CommandInput = (p: any) => <input {...p} className="border rounded px-2 py-1 w-full"/>;
export const CommandList = Box; export const CommandEmpty = Box; export const CommandGroup = Box; export const CommandItem = (p: any) => <div {...p} />; export const CommandSeparator = Box;

// Tooltip stubs
export const TooltipProvider = Box; export const Tooltip = Box; export const TooltipTrigger = (p: any) => <span {...p} />; export const TooltipContent = Box;

// Dialog stubs
export const Dialog = Box; export const DialogContent = Box; export const DialogHeader = Box; export const DialogTitle = Box; export const DialogDescription = Box;

// Icon stubs
const Icon: React.FC<{ className?: string }> = ({ className = "inline-block w-4 h-4" }) => <span className={className} aria-hidden>■</span>;
export const Database = Icon; export const FilePlus2 = Icon; export const BookOpenText = Icon; export const Filter = Icon; export const Download = Icon;
export const CheckCircle2 = Icon; export const ChevronRight = Icon; export const Info = Icon; export const HelpCircle = Icon; export const Sparkles = Icon;
export const Search = Icon; export const Keyboard = Icon; export const Eye = Icon;

export default {};
