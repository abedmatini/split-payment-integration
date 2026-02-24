# Split Payment Integration - Laravel MVP

## 🎯 Project Mission

Test and practice **split payment functionality** in Laravel by implementing a real e-commerce platform that allows users to pay using a combination of:
1. **Store Reward Credit** (internal balance system)
2. **Credit Card** (via PayFast payment gateway)

This is an MVP (Minimum Viable Product) focused on learning and testing the mechanics of split payments in the South African context.

---

## 📋 Technical Stack & Decisions

### Framework & Tools
- **Laravel**: v11 (latest)
- **Frontend**: Tailwind CSS, Laravel Blade
- **Authentication**: Laravel Breeze (registration, login, email verification, password reset)
- **Database**: MySQL
- **Email**: Brevo (Sendinblue) for transactional emails
- **Containerization**: DDEV + Docker
- **Payment Gateway**: PayFast (South African - Sandbox for testing)

### Architecture Decisions

| Decision | Choice | Reason |
|----------|--------|--------|
| **Payment Gateway** | PayFast | Most popular in SA, good sandbox, reliable webhooks |
| **Rewards System** | Internal (Database) | Simple, MVP-focused, no external dependencies |
| **Cart Storage** | Database | Persistent across sessions |
| **Email Service** | Brevo | Free tier, reliable, good for testing |
| **Split Payment Logic** | User Checkbox | User chooses to apply rewards; automatic deduction from total |
| **Order Finalization** | Webhook | More reliable than redirect-based confirmation |
| **Order Status Flow** | pending → paid → completed | Simple, clear progression |

---

## 🛒 Feature Overview

### User Features
- ✅ User registration with email verification
- ✅ Login & password reset functionality
- ✅ Browse products (title, price, description)
- ✅ Shopping cart (add/remove items)
- ✅ Rewards balance (starts with R500 for new users)
- ✅ Checkout with split payment option:
  - Option 1: Pay full amount via credit card
  - Option 2: Use rewards + pay difference via credit card
- ✅ Order history

### Admin/System Features
- ✅ PayFast sandbox integration
- ✅ Webhook handler for payment verification
- ✅ Order status tracking
- ✅ Email notifications (order confirmation, payment success)

---

## 🚀 Quick Start Guide

### Prerequisites
- DDEV installed on your machine
- Git
- Brevo account (for email - free tier)
- PayFast sandbox account

### Step 1: Clone & Setup DDEV

```bash
cd split-payment-integration

# Initialize DDEV with Laravel
ddev config --project-type=laravel --docroot=public
ddev start
```

### Step 2: Create Laravel 11 Project

```bash
# Create fresh Laravel 11 project inside DDEV
ddev composer create laravel/laravel:^11.0 --remove-vcs

# Generate app key
ddev exec php artisan key:generate
```

### Step 3: Install Laravel Breeze

```bash
ddev exec composer require laravel/breeze --dev
ddev exec php artisan breeze:install blade
ddev exec npm install && npm run build
```

### Step 4: Database Setup

```bash
# Run migrations
ddev exec php artisan migrate

# Seed demo products and users
ddev exec php artisan db:seed
```

### Step 5: Configure Brevo (Email)

1. Create account at [Brevo.com](https://www.brevo.com)
2. Get your API key from settings
3. Update `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=your_brevo_email@gmail.com
MAIL_PASSWORD=your_smtp_key
MAIL_FROM_ADDRESS="support@splitpayment.local"
MAIL_FROM_NAME="Split Payment"
```

### Step 6: Configure PayFast Sandbox

1. Create account at [PayFast Sandbox](https://sandbox.payfast.co.za)
2. Get your Merchant Key and Passphrase
3. Update `.env`:
```env
PAYFAST_MERCHANT_ID=10000100
PAYFAST_MERCHANT_KEY=your_merchant_key
PAYFAST_PASSPHRASE=your_passphrase
PAYFAST_MODE=sandbox
PAYFAST_RETURN_URL=http://localhost:8000/payment/return
PAYFAST_NOTIFY_URL=http://localhost:8000/api/webhook/payfast
```

### Step 7: Access the App

```bash
ddev launch
```

App will be available at: `http://localhost:8000`

---

## 📦 Database Schema Overview

### Users Table
```
- id
- name
- email (verified_at)
- password
- rewards_balance (DEFAULT: 500.00 ZAR)
- created_at / updated_at
```

### Products Table
```
- id
- title
- description
- price (ZAR)
- created_at / updated_at
```

### Carts Table
```
- id
- user_id
- product_id
- quantity
- created_at / updated_at
```

### Orders Table
```
- id
- user_id
- total_amount (ZAR)
- rewards_used (ZAR)
- card_amount (ZAR)
- status (pending, paid, completed)
- payfast_reference (webhook confirmation)
- created_at / updated_at
```

### Order Items Table
```
- id
- order_id
- product_id
- quantity
- price_at_purchase (ZAR)
```

---

## 💳 Split Payment Flow

### User Perspective

1. **Browse & Add to Cart** → View products, add to shopping cart
2. **Checkout** → Review cart, see rewards balance (R500 default)
3. **Choose Payment Method**:
   - ☐ Use my rewards balance (checkbox)
   - If checked: Rewards are deducted from total
4. **Pay via PayFast** → Charge remaining amount to credit card
5. **Payment Confirmation** → Webhook verifies payment
6. **Order Complete** → Rewards deducted, order status updated

### System Perspective

```
Cart Total: R1000
User Rewards: R500

Scenario A - No Rewards
├─ Credit Card Charge: R1000
└─ Order Status: pending → paid → completed

Scenario B - With Rewards Checkbox
├─ Deduct Rewards: -R500
├─ Remaining (Card): R500
├─ Credit Card Charge: R500
├─ Webhook confirms payment
├─ Deduct R500 from user_rewards_balance
└─ Order Status: pending → paid → completed
```

---

## 🧪 Testing Guide

### Test Card Numbers (PayFast Sandbox)

| Test Case | Card Number | Exp/CVV | Result |
|-----------|-------------|---------|--------|
| Success | 4532015112830366 | Any Future Date / Any CVV | ✅ Payment succeeds |
| Failure | 5425233010103442 | Any Future Date / Any CVV | ❌ Payment fails |

### Testing Split Payment Flow

#### Test 1: Full Card Payment (No Rewards)
1. Create account → Email verified
2. Add product to cart (any product)
3. Go to checkout
4. **Uncheck** "Use my rewards"
5. Proceed to PayFast
6. Use test card (success)
7. Verify order created with status "pending"
8. Page redirects/webhook fires → Order status becomes "paid"

#### Test 2: Split Payment (Rewards + Card)
1. Login as demo user (R500 rewards)
2. Add product worth R1000 to cart
3. Go to checkout
4. **Check** "Use my rewards"
5. Verify calculation:
   - Total: R1000
   - Rewards Used: R500
   - Card Charge: R500
6. Proceed to PayFast with R500
7. Use test card (success)
8. Webhook deducts R500 from user rewards balance
9. Verify new balance: R0

#### Test 3: Insufficient Rewards
1. Login as demo user (R500 rewards)
2. Add multiple products (total R2000)
3. Check "Use my rewards"
4. Verify:
   - Total: R2000
   - Rewards Available: R500
   - Only R500 applied (not more than available)
   - Card Charge: R1500

#### Test 4: Webhook Verification
1. Complete a payment successfully
2. Check console/logs for webhook payload from PayFast
3. Verify order status updates correctly
4. Verify rewards deducted from user account

### Local Webhook Testing

To test webhooks locally with DDEV:

```bash
# PayFast webhook tester:
# Login to PayFast sandbox → Developer tools → Test webhook
# Send manual webhook to: http://localhost:8000/api/webhook/payfast
```

---

## 📁 Project Structure

```
split-payment-integration/
├── app/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Order.php
│   │   └── OrderItem.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── ProductController.php
│   │   │   ├── CartController.php
│   │   │   ├── CheckoutController.php
│   │   │   ├── OrderController.php
│   │   │   └── WebhookController.php
│   └── Services/
│       ├── PayFastService.php
│       └── OrderService.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── views/
│   │   ├── products/
│   │   ├── cart/
│   │   ├── checkout/
│   │   └── orders/
│   └── css/
├── routes/
│   ├── web.php
│   └── api.php
├── .env.example
└── ddev.yaml
```

---

## 🔐 Security Notes

- **Webhook Validation**: All PayFast webhooks validated with merchant key signature
- **CSRF Protection**: All forms protected with Laravel CSRF tokens
- **Rewards Deduction**: Only after payment confirmation (via webhook)
- **Email Verification**: Required before checkout
- **Input Validation**: All user inputs validated server-side

---

## 📚 Learning Goals

By completing this MVP, you'll learn:
1. ✅ Laravel 11 authentication with email verification
2. ✅ E-commerce cart & order management
3. ✅ Payment gateway integration (PayFast)
4. ✅ Webhook handling & payment verification
5. ✅ Database transactions for atomicity
6. ✅ Split payment logic & calculation
7. ✅ Email notifications with Brevo
8. ✅ DDEV + Laravel development workflow
9. ✅ Tailwind CSS with Laravel Blade
10. ✅ Laravel Breeze authentication scaffolding

---

## 🤝 Next Steps

- [ ] Setup DDEV environment
- [ ] Create Laravel 11 project
- [ ] Install Laravel Breeze
- [ ] Create database migrations
- [ ] Seed demo products
- [ ] Build product listing page
- [ ] Build shopping cart
- [ ] Integrate PayFast
- [ ] Create checkout flow
- [ ] Implement webhook handler
- [ ] Test split payment flow
- [ ] Deploy to production (optional)

---

## 📝 License

MIT