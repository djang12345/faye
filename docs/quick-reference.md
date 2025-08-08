# 🚀 Quick Reference - Testing Users

## 🔐 Admin Users

| Email | Password | Role |
|-------|----------|------|
| `admin@dps.gov.ph` | `admin123` | Super Admin |
| `system.admin@dps.gov.ph` | `system123` | System Admin |
| `dept.admin@dps.gov.ph` | `dept123` | Department Admin |

## 👥 Applicant Users

| Email | Password | Status |
|-------|----------|--------|
| `juan.delacruz@email.com` | `applicant123` | PENDING |
| `maria.garcia@email.com` | `applicant123` | PENDING |
| `pedro.santos@email.com` | `applicant123` | ACCEPTED |

## 🌐 Access URLs

- **Application**: http://localhost
- **Control Panel**: http://localhost/xampp-control.html
- **Development Server**: http://localhost:8000

## 🧪 Quick Test Commands

```bash
# Check system status
dps-manager status

# View logs
dps-manager logs

# Restart services
xampp-control restart

# Clear cache
dps-manager clear
```

## 📋 Sample Data Summary

### Admin Users (3)
- Super Administrator with full access
- System Administrator for user management
- Department Administrator for processing

### Applicant Users (3)
- 2 PENDING applications
- 1 ACCEPTED application with interview scheduled
- Complete parent information for all applicants

### Database Records
- **Users**: 6 (3 admin + 3 applicant)
- **Applicants**: 3
- **Parents**: 3
- **Applications**: 3

---

**Last Updated**: August 7, 2025
**Status**: ✅ Ready for Testing
