# WindowTrip — Travel Agency Platform · Build Plan

Travel agency platform: public marketing website + client portal + custom admin portal.
Services: **Tourist Visa Processing**, **Air Ticket** (request-based, no flight search), **Tour Packages**.

---

## 1. Tech Stack (locked)

| Layer | Choice | Notes |
|---|---|---|
| Backend | **Laravel 12**, PHP 8.4 | Runs on Laravel Herd locally |
| Database | **MySQL** | Herd-bundled local, cPanel MySQL in prod |
| Public site | **Blade + Tailwind + Vue animation islands** | Full SEO, no Node needed in prod |
| Client portal + Admin | **Inertia.js + Vue 3 + Tailwind** | SPA feel, rich animation, behind login (SEO N/A) |
| Animation | **GSAP** (scroll/timeline), **Lenis** (smooth scroll), **@vueuse/motion** (micro-interactions), Vue `<Transition>` | |
| UI primitives | **Headless UI / shadcn-vue** (fully restyled) | No Filament — custom UI |
| Auth | **Laravel Breeze** (email/password) | Client + staff |
| Roles | **Spatie laravel-permission** | Admin / Agent / etc. |
| Build | **Vite** | Static asset build at deploy |
| Payments | **None online (manual)** | Bank transfer + receipt upload, admin confirms |

**Hosting target:** shared **cPanel** (no Node.js). Docroot → Laravel `/public`. Assets pre-built with Vite. No long-running Node process in production.

### Assumptions (confirm or correct)
- Currency: **BDT (৳)** primary
- Language: **English** first (Bangla can be added later via Laravel localization)
- Notifications: **Email** for now (status updates, doc requests); SMS/WhatsApp later
- Login: **email + password** (Google login / OTP later)

---

## 1b. Branding

- **Name:** Window Trip (two words)
- **Tagline:** _Your Complete Travel Partner_
- **Logo:** stylized "W" with airplane + motion swooshes; gradient sky-blue → purple/magenta
- **Assets:** primary logo (square/stacked) + horizontal icon-lockup (header). Store in `public/brand/`.

### Brand color palette (Tailwind tokens — to fine-tune against final logo)
| Token | Hex (approx) | Use |
|---|---|---|
| `brand-blue` | `#33A9DC` | primary blue (the W) |
| `brand-cyan` | `#27B6D6` | light accent |
| `brand-purple` | `#8E4D9E` | secondary |
| `brand-magenta` | `#B45C9C` | gradient end / accents |
| `brand-ink` | `#1B1B2F` | headings / "WINDOW TRIP" text |
| `brand-gradient` | `linear-gradient(135deg,#33A9DC,#8E4D9E,#B45C9C)` | hero, buttons, highlights |

- Use the gradient for hero backgrounds, primary CTAs, active states, and animated underlines.
- Headings in a clean geometric sans (e.g. Poppins/Montserrat) to match the logotype; body in a readable sans (Inter).
- Animated airplane/swoosh motif (GSAP/Lenis) as a recurring micro-brand element.

## 2. The Three Surfaces

### A. Public Website (Blade, SEO-first)
- Home (animated hero, services overview, featured packages, destinations, testimonials, CTA)
- Visa Processing — overview + **one page per country** (India, USA, Europe, Thailand, Singapore, Malaysia, China): requirements, documents, fees, processing time, FAQ, inquiry CTA
- Air Ticket — info + request form
- Tour Packages — listing + filters + single package page (itinerary, gallery, inclusions, price, booking CTA)
- About, Contact (lead form), Blog (optional, SEO), Terms/Privacy
- SEO: per-page meta/OG tags, JSON-LD, `sitemap.xml`, `robots.txt`, clean slugs

### B. Client Portal (Inertia + Vue, behind login)
1. **Dashboard** — active applications, bookings, unread messages, action items
2. **Visa Applications** — start per-country application, dynamic form, **multi-file document upload**, live status tracking
3. **Traveler Profiles** — save self + family/traveler details (passport, photo) and reuse across applications
4. **Bookings & Orders** — air ticket requests + package bookings, quotes, invoices, payment status (manual)
5. **Messaging / Support** — thread per application/booking (standalone support tickets deferred to post-v1)
6. **Profile & settings** — account, password, notifications

### C. Admin Portal (Inertia + Vue, custom UI, role-gated)
- Dashboard — KPIs (new leads, pending applications, awaiting docs, bookings) **+ charts (applications by country, bookings by month, lead→booking conversion)**
- Visa: manage country configs (requirements, docs, fees), review applications, change status, request docs, internal notes
- Air tickets: view requests, create/send quotes, upload issued tickets
- Packages: CRUD packages (itinerary, gallery, pricing, availability), manage bookings
- Leads/inquiries from public forms → assign to agent → convert
- Invoices: create, mark paid (manual), download
- Messaging: respond to client threads (attached to applications/bookings)
- Content: visa country pages, testimonials, site settings
- _Deferred to post-v1: Blog, general (standalone) support tickets_
- Users & roles: staff accounts, permissions; client list
- Activity log / audit trail

---

## 3. Service Workflows

### Visa Application
`Draft → Submitted → Under Review → Docs Required → Lodged → Approved / Rejected`
- Client picks country → form driven by that country's config → uploads docs → submits
- Admin reviews → may set **Docs Required** (client notified, uploads more) → updates status
- Status changes notify client by email; full timeline visible to both sides

### Air Ticket (no search)
`Request → Quoted → Accepted → Issued → Cancelled`
- Client submits route/dates/passengers/class/notes
- Admin sources fare manually → sends **quote** → client accepts → admin uploads ticket + invoice

### Tour Package
`Inquiry/Booking → Confirmed → Invoiced → Completed / Cancelled`
- Admin builds packages; client requests booking → admin confirms → invoice (manual payment)

---

## 4. Data Model (high level)

- **users** (clients + staff, role-flagged)
- **roles / permissions** (Spatie) — custom role set (TBD by client), granular permissions
- **traveler_profiles** (belongs to user: name, passport no/expiry, DOB, nationality, photo)
- **countries** — visa config: name, slug, content, fees, processing time, active
- **country_requirements** / **country_documents** — checklist + required doc types per country
- **visa_applications** — user, country, status, assigned_agent, submitted_at, notes
- **application_documents** — application, traveler_profile, doc_type, file, status
- **application_status_history** — timeline of status changes
- **ticket_requests** — user, route, dates, passengers, class, status
- **quotes** — ticket_request/booking, amount, valid_until, status, file
- **packages** — title, slug, destination, itinerary, inclusions, price, gallery, dates, active
- **package_bookings** — user, package, travelers, status
- **invoices** — polymorphic (application/ticket/booking), amount, status, receipt upload
- **leads** — public inquiries (name, contact, service, message, source, status)
- **conversations / messages** — threads attached to application/booking/ticket
- **settings** — site/contact/SEO config
- **activity_log** — audit (spatie/laravel-activitylog)

---

## 5. Build Phases

**Phase 0 — Scaffold**
Laravel 12 install, Tailwind, Inertia+Vue, Breeze auth, Spatie permissions, base layouts (public/portal/admin), Vite, Git. Animation libs wired (GSAP, Lenis, @vueuse/motion).

**Phase 1 — Public Website (SEO)**
Home, services, **per-country visa pages**, packages listing + single, about, contact + lead capture. Animations, meta/OG/JSON-LD, sitemap/robots.

**Phase 2 — Admin Foundation**
Admin shell + auth/roles, dashboard, manage countries (visa config), packages CRUD, leads inbox, staff/users, site settings.

**Phase 3 — Client Portal: Visa**
Registration/login, traveler profiles, start application, dynamic per-country form, document upload, status tracking + timeline.

**Phase 4 — Bookings: Tickets & Packages**
Ticket request flow + admin quotes + issued ticket upload. Package booking flow. Invoices (manual) + receipt upload.

**Phase 5 — Messaging & Notifications**
Threaded messaging per application/booking, support tickets, email notifications (status, docs required, quotes, invoices).

**Phase 6 — Polish & Launch**
Responsive QA, animation polish, accessibility, performance, SEO audit, content load, cPanel deploy + docs.

---

## 6. cPanel Deployment Notes
- Build assets locally/CI: `npm run build` → commit/upload `public/build`
- Upload app; set domain docroot → `/public`
- `composer install --no-dev`, configure `.env`, `php artisan migrate --force`, `storage:link`
- `php artisan config:cache route:cache view:cache`
- Cron: `* * * * * php /path/artisan schedule:run`
- Documents stored in `storage/app` → served via signed routes (not public)

---

## 6b. Build progress (as of this session)

**Done & verified:**
- Phase 0 scaffold (Laravel 13 + Inertia/Vue + Tailwind + brand + animation libs)
- Public site: animated home (9 sections), 7 visa country pages w/ requirements, visa dropdown menu
- **Multi-step visa application wizard** (`/visa/{country}/apply`) → saves to `visa_applications`, file uploads, reference number, success page
- **Email notifications** (applicant confirmation + admin alert) on submit — mail driver `log` in dev
- Branded **auth pages** (split-screen login/register)
- **Admin portal** (`/admin`): dashboard (KPIs + charts), applications list/filter/detail, status updates, private document viewer, **leads inbox**
- **Client portal** dashboard: user's applications + status tracker
- Public **Contact** (lead form), **Air Tickets** (request form), **Tour Packages** listing — forms save to `leads`
- Admin login: `admin@windowtrip.test` / `password` (seeded). Roles: admin, agent.

**DB tables added:** `visa_applications`, `leads` (+ Spatie permission/activity tables).

**Next candidates:** move visa/package data into DB (admin-managed), package booking flow, lead → application conversion, status-change email to applicant, mobile nav, real content/photos, custom roles, SMTP for production.

## 7. Open Questions
1. Confirm currency = BDT, language = English-first?
2. Email provider for notifications (SMTP via cPanel / Mailgun / etc.)?
3. Do you have logo + brand colors + copy, or use placeholders to start?
4. Blog needed at launch or later?
5. Roughly how many staff users / role types (Admin, Agent, ...)?
