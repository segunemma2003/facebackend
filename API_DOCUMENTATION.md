# System Settings API Documentation

## Base URL

```
http://your-domain.com/api/v1/system
```

## Authentication

**No authentication required** - All endpoints are completely public and accessible without any API tokens.

## Endpoints Overview

### 1. Home Settings (Single Record)

**Base Path:** `/home-settings`

#### GET `/home-settings`

Retrieve home page settings (returns single record)

-   **Response:** Returns the first (and typically only) home settings record
-   **Fields:** hero_title, hero_description, current_program_title, current_program_description, coming_soon, timer, event_date, is_button, button_text, button_link, about_title, about_description, section_face_1, section_face_2, section_pics

#### GET `/home-settings/{id}`

Retrieve specific home settings by ID

-   **Response:** Returns the specified home settings record

---

### 2. Footer Settings (Single Record)

**Base Path:** `/footer-settings`

#### GET `/footer-settings`

Retrieve footer settings (returns single record)

-   **Response:** Returns the first footer settings record
-   **Fields:** facebook_link, is_facebook, twitter_link, is_twitter, instagram_link, is_instagram, footer_text

#### GET `/footer-settings/{id}`

Retrieve specific footer settings by ID

---

### 3. General Global Settings (Single Record)

**Base Path:** `/general-global-settings`

#### GET `/general-global-settings`

Retrieve general global settings (returns single record)

-   **Response:** Returns the first general global settings record
-   **Fields:** address, email, international_phone, office_hours, location, motto

#### GET `/general-global-settings/{id}`

Retrieve specific general global settings by ID

---

### 4. About Settings (Single Record)

**Base Path:** `/about-settings`

#### GET `/about-settings`

Retrieve about page settings (returns single record)

-   **Response:** Returns the first about settings record
-   **Fields:** about_title, about_sub_title, about_number_recipient, about_number_countries, about_number_categories, about_body, mission, vision

#### GET `/about-settings/{id}`

Retrieve specific about settings by ID

---

### 5. Success Stories (Array of Items)

**Base Path:** `/success-stories`

#### GET `/success-stories`

Retrieve all success stories

-   **Response:** Returns array of all success stories
-   **Fields:** image, title, sub_title, sub_header, description

#### GET `/success-stories/{id}`

Retrieve specific success story by ID

---

### 6. Our Team (Array of Items)

**Base Path:** `/our-team`

#### GET `/our-team`

Retrieve all team members

-   **Response:** Returns array of all team members
-   **Fields:** image, name, position, description

#### GET `/our-team/{id}`

Retrieve specific team member by ID

---

### 7. Advisory Board (Array of Items)

**Base Path:** `/advisory-board`

#### GET `/advisory-board`

Retrieve all advisory board members

-   **Response:** Returns array of all advisory board members
-   **Fields:** image, name, title, region, expertise

#### GET `/advisory-board/{id}`

Retrieve specific advisory board member by ID

---

### 8. Our Approach (Array of Items)

**Base Path:** `/our-approach`

#### GET `/our-approach`

Retrieve all approach steps (ordered by step number)

-   **Response:** Returns array of all approach steps ordered by step
-   **Fields:** step, title, description, image

#### GET `/our-approach/{id}`

Retrieve specific approach step by ID

---

### 9. Contact Submissions (Array of Items)

**Base Path:** `/contacts`

#### GET `/contacts`

Retrieve all contact submissions (ordered by creation date, newest first)

-   **Response:** Returns array of all contact submissions
-   **Fields:** first_name, last_name, email, how_can_we_help, message, created_at

#### GET `/contacts/{id}`

Retrieve specific contact submission by ID

---

## Response Format

All API responses follow this standard format:

### Success Response

```json
{
    "success": true,
    "data": {...},
    "message": "Operation completed successfully"
}
```

### Data Structure

-   **Settings Endpoints** (home-settings, footer-settings, general-global-settings, about-settings):

    ```json
    {
      "success": true,
      "data": {
        "id": 1,
        "hero_title": "Welcome",
        "hero_description": "Description here",
        ...
      },
      "message": "Home settings retrieved successfully"
    }
    ```

-   **Content Endpoints** (success-stories, our-team, advisory-board, our-approach, contacts):
    ```json
    {
      "success": true,
      "data": [
        {
          "id": 1,
          "title": "First Item",
          ...
        },
        {
          "id": 2,
          "title": "Second Item",
          ...
        }
      ],
      "message": "Data retrieved successfully"
    }
    ```

## Status Codes

-   **200:** Success (GET)
-   **404:** Not Found

## Example Usage

### Get Home Settings (Single Record)

```bash
curl -X GET "http://your-domain.com/api/v1/system/home-settings"
```

### Get All Success Stories (Array)

```bash
curl -X GET "http://your-domain.com/api/v1/system/success-stories"
```

### Get Specific Team Member

```bash
curl -X GET "http://your-domain.com/api/v1/system/our-team/1"
```

## Notes

1. **Settings Resources:** Home Settings, Footer Settings, General Global Settings, and About Settings return single records representing the current configuration.

2. **Content Resources:** Success Stories, Our Team, Advisory Board, Our Approach, and Contact Submissions return arrays of all available records.

3. **No Authentication:** All endpoints are completely public and accessible without any API keys or authentication.

4. **Read-Only:** All endpoints are GET-only - no POST, PUT, PATCH, or DELETE operations are available.

5. **Image Fields:** Image fields contain URLs to uploaded images. Use your media upload system to handle file uploads separately.

6. **Ordering:**
    - Our Approach steps are ordered by step number
    - Contact submissions are ordered by creation date (newest first)
    - Other content endpoints return records in default order
