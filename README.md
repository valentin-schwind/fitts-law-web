
# Fitts Law 2D Task (ISO 9241-9) – Web Study

Browser-based implementation of a 2D Fitts’ Law pointing task (ISO 9241-9-style circular tapping), including a demographics + self-report questionnaire, movement/click logging, and server-side persistence (PHP + MySQL).

The task measures pointing performance and computes standard Fitts metrics such as movement time (MT), error rate, effective width (We), effective ID (IDe), and throughput (TP, bit/s).

A running demo of the project can be found here: https://hci-studies.org/fitts-law-web/.

## Features

- **ISO 9241-9-style 2D task**
  - Circular target layout with alternating target selection
  - Configurable amplitudes (A), widths (W), trials/condition
  - Canvas-based rendering
- **Questionnaire (pre-task)**
  - Demographics (age, gender, nationality, occupation, degree, etc.)
  - Environment & setup (noise, lighting, posture, device, displays)
  - Familiarity with Fitts’ Law, handedness
  - Likert-based emotion items
  - **BFI-10** personality items
  - Informed consent accordion (`terms.html`)
- **Logging**
  - Trial-level click log (hit/miss, timing, coordinates, condition)
  - High-frequency movement trace (throttled sampling)
  - Browser/device metadata (screen, DPR, user-agent, touch support; optional performance.memory)
- **Computation & output**
  - Condition-wise stats (Ae, We, IDe, MT, TP)
  - Overall means and **linear regression** (MT = a + b · IDe, with R²)
  - Console tables + on-screen summary
- **Data submission**
  - JSON payload POST to `php/submit.php`
  - MySQL schema for subjects, trial_log, movement_log, overall_stats
- **Participation certificate**
  - Client-side PDF generation via `html2pdf` (name prompt)

## Tech Stack

- HTML5 + Canvas
- JavaScript (jQuery)
- Bootstrap (CSS + icons)
- `jquery.cookie` for `subjectCode` and experiment count
- `html2pdf` for participation certificate PDF
- PHP endpoint for saving results (`php/submit.php`)
- MySQL (InnoDB, utf8mb4)

## Repository Structure (suggested)

```

.
├─ index.html
├─ terms.html
├─ country.txt
├─ css/
│  ├─ bootstrap.min.css
│  ├─ bootstrap-icons.css
│  └─ style.css
├─ js/
│  ├─ jquery-3.3.1.min.js
│  ├─ html2pdf.bundle.min.js
│  └─ jquery-cookie-plugin/
│     └─ jquery.cookie.js
├─ php/
│  └─ submit.php
└─ sql/
└─ schema.sql

````

## How the Task Works

- Targets are placed on a circle around the canvas center.
- For each condition (A × W), targets are generated with alternating steps so that successive selections are approximately opposite / alternating around the circle.
- Participants click the **highlighted target** as quickly and accurately as possible.
- A click is considered a **hit** if the distance from click position to target center is ≤ W/2.
- Hits advance the trial; misses are logged (error).

### Default Parameters (as in the code)

- `numberOfTrials = 15` (odd recommended)
- `repetitions = 1`
- **Debug mode**
  - `debug = true` uses fewer conditions for quick testing:
    - Amplitudes: `[400, 600]`
    - Widths: `[40, 80]`
- **Non-debug**
  - Amplitudes: `[100, 200, 400, 600]`
  - Widths: `[20, 40, 60, 80]`

Canvas is responsive and scaled to a max of 800×800; amplitudes and widths are scaled accordingly.

## Metrics

The implementation computes (per condition):

- **Ae** (effective amplitude) based on signed projection error (`dx`) and sequential correction
- **We** (effective target width) using `We = 4.133 * SD(dx)` (normal assumption)
- **IDe**: `log2(Ae / We + 1)`
- **MT**: mean movement time (s)
- **TP**: `IDe / MT` (bit/s)
- Error rate: misses / (hits + misses)

Additionally, it fits a linear model:

- `MT(ms) = a + b * IDe` and reports **R²**

## Data & Privacy Notes

- A pseudonymous `subjectCode` is stored in a cookie (`subject-YYYYMMDD-<hex>`).
- `expCount` is stored in a cookie to track repeated runs from the same browser.
- The system logs detailed movement traces; consider informing participants explicitly in your consent text.

## Setup

### 1) Static Files (Frontend)

Serve the directory with any static server, e.g.:

- Apache/Nginx
- PHP built-in server
- VS Code Live Server

Example:

```bash
php -S localhost:8000
````

Then open:

```
http://localhost:8000
```

### 2) Database (MySQL)

Create a database and run the schema (see `sql/schema.sql`).

Example:

```sql
CREATE DATABASE fitts_web CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fitts_web;

-- paste schema.sql contents here
```

The schema includes:

* `subjects`
* `trial_log`
* `movement_log`
* `overall_stats`

All logs link to `subjects(subject_id)` via foreign keys with `ON DELETE CASCADE`.

### 3) Backend Endpoint (PHP)

Implement `php/submit.php` to:

1. Parse JSON payload:

   * `demographicData`
   * `clickLog`
   * `movementLog`
   * `overallStats`
2. Insert / upsert the subject (or insert per experiment run)
3. Insert trial and movement rows
4. Insert overall stats row
5. Return JSON:

   * `{ "success": true }` on success
   * `{ "success": false, "error": "..." }` on failure

Minimal response contract is required because the client checks `resp.success`.

## Running a Study

1. Customize `terms.html` (informed consent) and confirm it matches what you log.
2. Ensure `country.txt` is present and readable (nationalities dropdown).
3. Decide whether to keep `debug = true` (testing) or `false` (full study).
4. Deploy to a HTTPS host (recommended for research deployments).

## Customization

Common changes are in `index.html`:

* Target conditions:

  * `baseAmplitudes`, `baseWidths`, `repetitions`, `numberOfTrials`
* Sampling rate for movement trace:

  * `sampleInterval` (ms)
* UI text and study branding:

  * Headline, instructions, logos, and footer links
* Stored fields:

  * Update `demographicData` mapping to align with your DB schema and consent text

## References

* Fitts, P. M. (1954). *The information capacity of the human motor system in controlling the amplitude of movement.*
* MacKenzie, I. S., & Buxton, W. (1992). *Extending Fitts’ law to two-dimensional tasks.*
* Soukoreff, R. W., & MacKenzie, I. S. (2004). *Towards a standard for pointing device evaluation…*
* ISO 9241-9: Requirements for non-keyboard input devices (historical standard; superseded within the ISO 9241 series).

## MIT License

Copyright (c) 2026 Valentin Schwind

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.


## Contact

* Prof. Dr. Valentin Schwind
  Email: [schwind@hdm-stuttgart.de](mailto:schwind@hdm-stuttgart.de)


