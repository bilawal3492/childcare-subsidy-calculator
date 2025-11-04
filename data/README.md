# Australian Suburbs Data

## Current Status
Suburbs are now stored in a **WordPress database table** (`wp_ccs_suburbs`) with 18,000+ Australian suburbs.

## Database-Driven System

The plugin now uses a modern database approach instead of static PHP files:
- ✅ Suburbs stored in database table
- ✅ AJAX-powered autocomplete
- ✅ Fast indexed searches
- ✅ Easy to update via admin panel

## How to Import Suburbs

### Automatic Import (Recommended)
1. Activate the plugin
2. Go to **Child Care Subsidy > Suburbs Database**
3. Click **"Import Suburbs Data"**
4. Wait 1-2 minutes for completion
5. Done! 18,000+ suburbs imported

### Manual Import (Advanced)
If automatic import fails, you can manually import data:

1. **Download Data**
   - Source: https://github.com/matthewproctor/australianpostcodes
   - Or: https://data.gov.au/ (search "postcodes")

2. **Import via Admin**
   - Go to Suburbs Database page
   - Click Import button
   - System fetches data from GitHub automatically

## Data Source

- **Primary**: Australian Postcodes Database (GitHub)
- **URL**: https://raw.githubusercontent.com/matthewproctor/australianpostcodes/master/australian_postcodes.csv
- **Coverage**: 18,000+ suburbs across all states and territories
- **Updates**: Can be re-imported anytime to get latest data

## Database Structure

```sql
Table: wp_ccs_suburbs
Columns:
- id (bigint) - Primary key
- suburb (varchar) - Suburb name
- postcode (varchar) - 4-digit postcode
- state (varchar) - State/territory code

Indexes:
- suburb_idx - Fast suburb name searches
- postcode_idx - Fast postcode searches
- state_idx - Filter by state
```

## Features

- ✅ **18,000+ suburbs** - Complete Australian coverage
- ✅ **Fast searches** - Indexed database queries
- ✅ **AJAX autocomplete** - Real-time suggestions
- ✅ **State filtering** - Search by state/territory
- ✅ **Easy updates** - One-click re-import
- ✅ **No file limits** - Unlimited suburbs supported
