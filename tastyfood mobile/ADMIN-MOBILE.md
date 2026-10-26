# PADANG RICE ADMIN MOBILE APP - TECHNICAL SPECIFICATION

> **Target Audience**: AI Agents & Mobile Developers building React Native, Flutter, or native iOS/Android admin app  
> **Version**: 1.0  
> **Last Updated**: October 2026  
> **Base API**: Laravel 13.34 REST API (existing web application)

---

## TABLE OF CONTENTS

1. [Overview & Architecture](#1-overview--architecture)
2. [Authentication System](#2-authentication-system)
3. [Dashboard & Analytics](#3-dashboard--analytics)
4. [Order Management](#4-order-management)
5. [Payment Verification](#5-payment-verification)
6. [Real-time Chat](#6-real-time-chat)
7. [Menu Management](#7-menu-management)
8. [Content Management](#8-content-management)
9. [Settings & Configuration](#9-settings--configuration)
10. [Push Notifications](#10-push-notifications)
11. [Offline Capabilities](#11-offline-capabilities)
12. [API Endpoints Reference](#12-api-endpoints-reference)
13. [Data Models](#13-data-models)
14. [UI/UX Guidelines](#14-uiux-guidelines)
15. [Testing & QA](#15-testing--qa)

---

## 1. OVERVIEW & ARCHITECTURE

### 1.1 Application Purpose

**Padang Rice Admin Mobile** is a companion mobile application for restaurant administrators to manage orders, payments, and customer interactions on-the-go. It provides core operational functions without the full CMS features available on web.

### 1.2 Target Users

- **Restaurant Managers**: Monitor business metrics, approve payments
- **Kitchen Staff**: View incoming orders, update cooking status
- **Customer Support**: Respond to chat messages, handle complaints
- **Delivery Coordinators**: Track delivery orders, update in-transit status

### 1.3 Technology Stack Options

**Recommended: React Native**
- Single codebase for iOS & Android
- Fast development with Expo
- Strong ecosystem for maps, push notifications, image handling

**Alternative: Flutter**
- Better performance for complex animations
- Excellent hot reload
- Growing ecosystem

**Native (iOS Swift / Android Kotlin)**
- Best performance & platform integration
- Higher development cost
- Use if budget allows

### 1.4 Architecture Pattern

```
┌─────────────────────────────────────┐
│     Mobile App (React Native)       │
├─────────────────────────────────────┤
│  ┌─────────────┐  ┌──────────────┐ │
│  │ Redux/Zustand│  │ React Query │ │
│  │ State Mgmt   │  │ API Cache   │ │
│  └─────────────┘  └──────────────┘ │
├─────────────────────────────────────┤
│  ┌─────────────┐  ┌──────────────┐ │
│  │ REST API    │  │ WebSocket    │ │
│  │ HTTP Client │  │ Real-time    │ │
│  └─────────────┘  └──────────────┘ │
└─────────────────────────────────────┘
           ↓              ↓
┌─────────────────────────────────────┐
│   Laravel Backend (Web App)         │
│   - Auth: Sanctum tokens            │
│   - API: JSON responses             │
│   - WebSocket: Laravel Echo         │
└─────────────────────────────────────┘
```

### 1.5 Core Dependencies

**React Native:**
```json
{
  "dependencies": {
    "@react-navigation/native": "^6.x",
    "@react-navigation/stack": "^6.x",
    "@tanstack/react-query": "^5.x",
    "axios": "^1.x",
    "react-native-maps": "^1.x",
    "react-native-push-notification": "^8.x",
    "react-native-image-picker": "^7.x",
    "react-native-vector-icons": "^10.x",
    "zustand": "^4.x",
    "dayjs": "^1.x"
  }
}
```

**Flutter:**
```yaml
dependencies:
  flutter_bloc: ^8.1.0
  dio: ^5.0.0
  google_maps_flutter: ^2.5.0
  firebase_messaging: ^14.0.0
  image_picker: ^1.0.0
  cached_network_image: ^3.3.0
```

---

## 2. AUTHENTICATION SYSTEM

### 2.1 Login Flow

**Endpoint:** `POST /admin/login`

**Request:**
```json
{
  "email": "admin@tastyfood.com",
  "password": "password123",
  "device_name": "iPhone 14 Pro"
}
```

**Response (Success):**
```json
{
  "success": true,
  "token": "1|laravel_sanctum_token_here",
  "admin": {
    "id": 1,
    "name": "Admin Padang Rice",
    "email": "admin@tastyfood.com",
    "created_at": "2026-01-01T00:00:00Z"
  }
}
```

**Response (Error):**
```json
{
  "success": false,
  "message": "Email atau password salah"
}
```

### 2.2 Token Storage

**Secure Storage Required:**
- iOS: Keychain
- Android: Encrypted SharedPreferences
- React Native: `@react-native-async-storage/async-storage` + encryption

**Token Lifecycle:**
1. Store token after successful login
2. Include in all API requests: `Authorization: Bearer {token}`
3. Handle 401 Unauthorized → force logout
4. Refresh token not implemented (use long-lived tokens)

### 2.3 Logout Flow

**Endpoint:** `POST /admin/logout`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

**Client Actions:**
1. Call logout endpoint
2. Clear stored token
3. Clear app state/cache
4. Navigate to login screen

### 2.4 Session Validation

**Check on App Launch:**
```javascript
async function initializeApp() {
  const token = await getStoredToken();
  
  if (!token) {
    navigateToLogin();
    return;
  }
  
  try {
    // Validate token with a lightweight endpoint
    const response = await axios.get('/admin', {
      headers: { Authorization: `Bearer ${token}` }
    });
    
    if (response.data.success) {
      navigateToDashboard();
    }
  } catch (error) {
    if (error.response?.status === 401) {
      await clearToken();
      navigateToLogin();
    }
  }
}
```

---

## 3. DASHBOARD & ANALYTICS

### 3.1 Dashboard Overview

**Endpoint:** `GET /admin`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Response:**
```json
{
  "success": true,
  "stats": {
    "total_news": 12,
    "total_galleries": 45,
    "unread_messages": 3
  },
  "recentNews": [
    {
      "id": 1,
      "title": "Promo Ramadan 2026",
      "image": "https://tastyfood.com/storage/news/promo.jpg",
      "created_at_human": "2 jam yang lalu"
    }
  ],
  "recentMessages": [
    {
      "id": 5,
      "name": "John Doe",
      "subject": "Pertanyaan tentang delivery",
      "is_read": false,
      "created_at_human": "5 menit yang lalu"
    }
  ]
}
```

### 3.2 Key Metrics Cards

**Display in Dashboard:**

1. **Pending Orders Count**
   - Source: Filter orders with `status: 'pending'`
   - Update frequency: Every 30 seconds (polling or push)
   - Tap action: Navigate to Orders → Pending filter

2. **Payments Awaiting Verification**
   - Source: Filter payments with `status: 'waiting_verification'`
   - Update frequency: Real-time (critical)
   - Tap action: Navigate to Payment Verification screen

3. **Unread Chat Messages**
   - Source: Conversations with unread messages
   - Update frequency: Real-time via WebSocket
   - Tap action: Navigate to Chat Inbox

4. **Today's Revenue**
   - Source: Sum of paid orders today
   - Calculation: Client-side from orders API
   - Display: Rp format with thousands separator

### 3.3 Quick Actions Widget

**Bottom Sheet or Action Bar:**
- New Order Notification (badge)
- Scan QR Code (for payment verification)
- Quick Chat Reply
- Emergency Alert (close restaurant temporarily)

---

## 4. ORDER MANAGEMENT

### 4.1 Order List

**Endpoint:** `GET /admin/pesanan`

**Query Parameters:**
```
?status={all|pending|confirmed|preparing|ready|in_transit|delivered|completed|cancelled}
&payment_status={all|unpaid|waiting_verification|paid|rejected}
&search={order_number|customer_name|phone}
&page={1}
```

**Response:**
```json
{
  "success": true,
  "orders": [
    {
      "id": 12,
      "order_number": "ORD-20261013045500",
      "customer_name": "Budi Santoso",
      "total": 125000,
      "status": "preparing",
      "payment_status": "paid",
      "created_at": "2026-10-13T04:55:00Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 20,
    "total": 95
  }
}
```

### 4.2 Order Detail

**Endpoint:** `GET /admin/pesanan/{order_id}`

**Response:**
```json
{
  "success": true,
  "order": {
    "id": 12,
    "order_number": "ORD-20261013045500",
    "customer_name": "Budi Santoso",
    "customer_email": "budi@example.com",
    "customer_phone": "081234567890",
    "delivery_method": "delivery",
    "delivery_address": "Jl. Sudirman No. 123, Jakarta",
    "latitude": -6.200000,
    "longitude": 106.816666,
    "status": "preparing",
    "subtotal": 120000,
    "delivery_fee": 5000,
    "total": 125000,
    "notes": "Jangan pakai cabe",
    "created_at": "2026-10-13T04:55:00Z",
    "items": [
      {
        "id": 25,
        "menu_id": 3,
        "menu_name": "Rendang Sapi",
        "menu_price": 40000,
        "quantity": 2,
        "subtotal": 80000,
        "menu_image": "https://tastyfood.com/storage/menus/rendang.jpg"
      },
      {
        "id": 26,
        "menu_id": 5,
        "menu_name": "Nasi Putih",
        "menu_price": 5000,
        "quantity": 2,
        "subtotal": 10000,
        "menu_image": "https://tastyfood.com/storage/menus/nasi.jpg"
      },
      {
        "id": 27,
        "menu_id": 8,
        "menu_name": "Es Teh Manis",
        "menu_price": 5000,
        "quantity": 6,
        "subtotal": 30000,
        "menu_image": "https://tastyfood.com/storage/menus/esteh.jpg"
      }
    ],
    "payment": {
      "id": 8,
      "status": "paid",
      "amount": 125000,
      "payment_method": {
        "id": 2,
        "name": "BCA Transfer",
        "type": "bank_transfer"
      },
      "proof_image": "https://tastyfood.com/storage/payments/proof_8.jpg",
      "verified_at": "2026-10-13T05:00:00Z"
    },
    "user": {
      "id": 5,
      "name": "Budi Santoso",
      "phone": "081234567890"
    }
  }
}
```

### 4.3 Update Order Status

**Endpoint:** `PATCH /admin/pesanan/{order_id}/status`

**Request:**
```json
{
  "status": "preparing"
}
```

**Valid Status Transitions:**
```
pending → confirmed (after payment verified)
confirmed → preparing (kitchen starts cooking)
preparing → ready (food ready for pickup/delivery)
ready → in_transit (delivery only, driver picked up)
ready → delivered (pickup completed)
in_transit → delivered (delivery completed)
delivered → completed (customer confirmed)
any → cancelled (with reason)
```

**Response:**
```json
{
  "success": true,
  "message": "Status pesanan diubah menjadi Diproses",
  "order": {
    "id": 12,
    "status": "preparing",
    "updated_at": "2026-10-13T05:10:00Z"
  }
}
```

### 4.4 Order Status UI Components

**Status Badge Colors:**
```javascript
const statusColors = {
  pending: { bg: '#FEF3C7', text: '#92400E' }, // yellow
  confirmed: { bg: '#DBEAFE', text: '#1E40AF' }, // blue
  preparing: { bg: '#FED7AA', text: '#9A3412' }, // orange
  ready: { bg: '#D1FAE5', text: '#065F46' }, // green
  in_transit: { bg: '#E9D5FF', text: '#6B21A8' }, // purple
  delivered: { bg: '#D1FAE5', text: '#065F46' }, // green
  completed: { bg: '#E5E7EB', text: '#374151' }, // gray
  cancelled: { bg: '#FEE2E2', text: '#991B1B' } // red
};
```

**Status Action Buttons:**
```javascript
const getNextActions = (currentStatus) => {
  switch (currentStatus) {
    case 'pending':
      return ['Batalkan']; // Wait for payment
    case 'confirmed':
      return ['Mulai Proses', 'Batalkan'];
    case 'preparing':
      return ['Tandai Siap'];
    case 'ready':
      return ['Dalam Perjalanan', 'Selesai (Pickup)', 'Batalkan'];
    case 'in_transit':
      return ['Tandai Dikirim'];
    case 'delivered':
      return ['Selesaikan'];
    default:
      return [];
  }
};
```

### 4.5 Order Map View (Delivery Orders)

**Component Requirements:**
- Display restaurant location (from settings)
- Display delivery destination (order.latitude, order.longitude)
- Draw route line between points
- Show distance & estimated time
- Tap to open in Google Maps / Apple Maps

**Implementation (React Native):**
```javascript
import MapView, { Marker, Polyline } from 'react-native-maps';

function OrderMapView({ order, restaurantLocation }) {
  const region = {
    latitude: (order.latitude + restaurantLocation.latitude) / 2,
    longitude: (order.longitude + restaurantLocation.longitude) / 2,
    latitudeDelta: 0.05,
    longitudeDelta: 0.05
  };
  
  return (
    <MapView region={region}>
      <Marker
        coordinate={restaurantLocation}
        title="Padang Rice"
        pinColor="red"
      />
      <Marker
        coordinate={{
          latitude: order.latitude,
          longitude: order.longitude
        }}
        title={order.customer_name}
        pinColor="blue"
      />
      <Polyline
        coordinates={[restaurantLocation, {
          latitude: order.latitude,
          longitude: order.longitude
        }]}
        strokeColor="#000"
        strokeWidth={2}
      />
    </MapView>
  );
}
```

---

## 5. PAYMENT VERIFICATION

### 5.1 Pending Payments List

**Endpoint:** `GET /admin/pesanan?payment_status=waiting_verification`

**Response:** (Same structure as order list, filtered)

**UI Requirements:**
- Sort by oldest first (FIFO queue)
- Show payment amount prominently
- Preview thumbnail of proof image
- Swipe actions: Approve (green) / Reject (red)

### 5.2 Payment Detail View

**Show:**
1. Order summary (customer, items, total)
2. Payment method details
3. **Full-screen proof image** (pinch to zoom)
4. Upload timestamp
5. User notes (if any)

**Actions:**
- Approve button (large, green)
- Reject button (requires reason)
- Call customer (phone number)
- Open proof in external viewer

### 5.3 Approve Payment

**Endpoint:** `POST /admin/order-management/payment/{payment_id}/approve`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Pembayaran disetujui",
  "payment": {
    "id": 8,
    "status": "paid",
    "verified_at": "2026-10-13T05:15:00Z",
    "verified_by": 1
  },
  "order": {
    "id": 12,
    "status": "confirmed"
  }
}
```

**Client Actions After Approval:**
1. Show success toast
2. Remove from pending list
3. Update order status to "confirmed"
4. Send push notification to customer (backend handles this)
5. Play success sound (optional)

### 5.4 Reject Payment

**Endpoint:** `POST /admin/order-management/payment/{payment_id}/reject`

**Request:**
```json
{
  "rejection_reason": "Bukti transfer tidak jelas. Mohon upload ulang dengan foto yang lebih jelas."
}
```

**Response:**
```json
{
  "success": true,
  "message": "Pembayaran ditolak",
  "payment": {
    "id": 8,
    "status": "rejected",
    "rejection_reason": "Bukti transfer tidak jelas..."
  }
}
```

**UI Flow:**
1. Tap Reject button
2. Show modal with textarea for reason
3. Validate reason (min 10 characters)
4. Confirm action (double confirmation)
5. Submit request
6. Show success + remove from list
7. Customer receives rejection notification

### 5.5 Quick Reject Reasons (Templates)

**Pre-defined rejection reasons:**
```javascript
const rejectionTemplates = [
  "Bukti transfer tidak jelas. Mohon upload ulang.",
  "Nama pengirim tidak sesuai dengan nama pemesan.",
  "Jumlah transfer tidak sesuai dengan total pesanan.",
  "Bukti transfer sudah pernah digunakan untuk pesanan lain.",
  "Format bukti transfer tidak valid.",
  // Allow custom reason
];
```

---

## 6. REAL-TIME CHAT

### 6.1 Chat Inbox

**Endpoint:** `GET /admin/chat`

**Query Parameters:**
```
?status={open|closed}
&search={customer_name}
```

**Response:**
```json
{
  "success": true,
  "conversations": [
    {
      "id": 3,
      "user": {
        "id": 5,
        "name": "Budi Santoso",
        "phone": "081234567890"
      },
      "order_id": 12,
      "order_number": "ORD-20261013045500",
      "status": "open",
      "last_message": "Terima kasih!",
      "last_message_at": "2026-10-13T05:20:00Z",
      "unread_count": 0
    },
    {
      "id": 2,
      "user": {
        "id": 3,
        "name": "Siti Aminah"
      },
      "order_id": null,
      "order_number": null,
      "status": "open",
      "last_message": "Pesanan saya dimana?",
      "last_message_at": "2026-10-13T05:18:00Z",
      "unread_count": 2
    }
  ]
}
```

**UI Components:**
- **Unread Badge:** Show unread count prominently
- **Last Message Preview:** Truncate to 50 chars
- **Timestamp:** Use relative time (e.g., "2m ago", "5h ago")
- **Order Link:** If order_id exists, show order number as clickable chip
- **Status Indicator:** Open (green dot) / Closed (gray)

### 6.2 Chat Messages

**Endpoint:** `GET /admin/chat/{conversation_id}/messages`

**Query Parameters:**
```
?before_id={message_id} // For pagination (load older)
&limit=50
```

**Response:**
```json
{
  "success": true,
  "conversation": {
    "id": 3,
    "user": {
      "id": 5,
      "name": "Budi Santoso"
    },
    "order_id": 12,
    "status": "open"
  },
  "messages": [
    {
      "id": 45,
      "uuid": "550e8400-e29b-41d4-a716-446655440000",
      "sender_type": "user",
      "sender_id": 5,
      "message": "Halo, pesanan saya sudah sampai mana?",
      "message_type": "text",
      "is_read": true,
      "created_at": "2026-10-13T05:15:00Z"
    },
    {
      "id": 46,
      "uuid": "660e8400-e29b-41d4-a716-446655440001",
      "sender_type": "admin",
      "sender_id": 1,
      "message": "Pesanan Anda sedang dalam perjalanan, estimasi 10 menit lagi.",
      "message_type": "text",
      "is_read": true,
      "created_at": "2026-10-13T05:16:00Z"
    },
    {
      "id": 47,
      "uuid": "770e8400-e29b-41d4-a716-446655440002",
      "sender_type": "system",
      "sender_id": 0,
      "message": "Status pesanan diubah menjadi Dalam Perjalanan",
      "message_type": "system",
      "is_read": true,
      "created_at": "2026-10-13T05:16:30Z"
    }
  ],
  "has_more": false
}
```

**Message Bubble Styling:**
```javascript
const messageStyles = {
  user: {
    align: 'left',
    bg: '#F3F4F6', // gray
    textColor: '#111827'
  },
  admin: {
    align: 'right',
    bg: '#1F2937', // dark gray/black
    textColor: '#FFFFFF'
  },
  system: {
    align: 'center',
    bg: '#FEF3C7', // light yellow
    textColor: '#92400E',
    italic: true
  }
};
```

### 6.3 Send Message

**Endpoint:** `POST /admin/chat/{conversation_id}/messages`

**Request:**
```json
{
  "message": "Pesanan Anda sedang diproses. Mohon tunggu."
}
```

**Response:**
```json
{
  "success": true,
  "message": {
    "id": 48,
    "uuid": "880e8400-e29b-41d4-a716-446655440003",
    "sender_type": "admin",
    "sender_id": 1,
    "message": "Pesanan Anda sedang diproses. Mohon tunggu.",
    "is_read": false,
    "created_at": "2026-10-13T05:25:00Z"
  }
}
```

**Optimistic UI Update:**
1. Show message immediately with "sending" indicator
2. Send request in background
3. On success: Replace with server response (use uuid to match)
4. On error: Show error icon, allow retry

### 6.4 Mark as Read

**Endpoint:** `POST /admin/chat/{conversation_id}/read`

**Action:** Mark all messages in conversation as read by admin

**Call When:**
- Admin opens chat screen
- Admin views new message notification

### 6.5 Typing Indicator

**Endpoint:** `POST /admin/chat/{conversation_id}/typing`

**Request:**
```json
{
  "is_typing": true
}
```

**Implementation:**
- Send `is_typing: true` when admin starts typing
- Send `is_typing: false` after 2 seconds of no typing
- Display "Customer is typing..." if customer sends typing event

### 6.6 Close Conversation

**Endpoint:** `POST /admin/chat/{conversation_id}/close`

**Response:**
```json
{
  "success": true,
  "message": "Percakapan ditutup"
}
```

**UI:**
- Move to "Closed" tab
- Disable message input
- Show "Reopen" button if needed

### 6.7 WebSocket Real-time Updates

**Channel:** `admin-chat`

**Events to Listen:**
```javascript
Echo.channel('admin-chat')
  .listen('NewChatMessage', (e) => {
    // Add new message to conversation
    addMessageToChat(e.conversation_id, e.message);
    // Update unread badge
    incrementUnreadCount();
    // Show notification if app in background
    showPushNotification(e);
  })
  .listen('UserTyping', (e) => {
    // Show typing indicator
    showTypingIndicator(e.conversation_id, e.user_name);
  });
```

**Fallback (No WebSocket):**
- Poll `/admin/chat-unread` every 15 seconds
- Poll active conversation messages every 10 seconds

---

## 7. MENU MANAGEMENT

### 7.1 Menu List

**Endpoint:** `GET /admin/menus`

**Query Parameters:**
```
?category={all|nasi|lauk|sayur|minuman}
&search={menu_name}
&available={1|0}
```

**Response:**
```json
{
  "success": true,
  "menus": [
    {
      "id": 3,
      "name": "Rendang Sapi",
      "description": "Rendang daging sapi empuk...",
      "price": 40000,
      "image": "https://tastyfood.com/storage/menus/rendang.jpg",
      "category": "lauk",
      "available": true,
      "order_count": 156,
      "created_at": "2026-01-15T00:00:00Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 2,
    "per_page": 20,
    "total": 28
  }
}
```

### 7.2 Toggle Menu Availability

**Endpoint:** `PATCH /admin/menus/{menu_id}`

**Request:**
```json
{
  "available": false
}
```

**Response:**
```json
{
  "success": true,
  "message": "Status menu diubah",
  "menu": {
    "id": 3,
    "available": false
  }
}
```

**UI:**
- Toggle switch on each menu card
- Instant feedback (optimistic update)
- Revert on error

### 7.3 Create Menu (Simplified)

**Endpoint:** `POST /admin/menus`

**Request (multipart/form-data):**
```
name: "Ayam Pop"
description: "Ayam goreng khas Padang..."
price: 25000
category: "lauk"
available: true
image: [File object]
```

**Response:**
```json
{
  "success": true,
  "message": "Menu berhasil ditambahkan",
  "menu": {
    "id": 15,
    "name": "Ayam Pop",
    "price": 25000,
    "image": "https://tastyfood.com/storage/menus/ayam-pop-1697012345.jpg"
  }
}
```

**Mobile Implementation:**
- Use image picker library
- Compress image before upload (max 2MB)
- Show upload progress
- Validate required fields

### 7.4 Edit Menu

**Endpoint:** `PUT /admin/menus/{menu_id}`

**Request:** (Same as create, all fields optional)

**Response:** (Same structure)

### 7.5 Delete Menu

**Endpoint:** `DELETE /admin/menus/{menu_id}`

**Response:**
```json
{
  "success": true,
  "message": "Menu berhasil dihapus"
}
```

**UI Flow:**
1. Swipe to delete OR long-press menu → show action sheet
2. Confirm deletion (alert dialog)
3. Submit request
4. Remove from list on success
5. Show undo snackbar (optional, requires soft delete)

---

## 8. CONTENT MANAGEMENT

### 8.1 News/Berita Management

**List Endpoint:** `GET /admin/berita`

**Response:**
```json
{
  "success": true,
  "news": [
    {
      "id": 5,
      "title": "Promo Spesial Bulan Oktober",
      "slug": "promo-spesial-bulan-oktober",
      "image": "https://tastyfood.com/storage/news/promo-okt.jpg",
      "content": "Dapatkan diskon 20% untuk...",
      "author": "Admin",
      "status": "published",
      "published_at": "2026-10-01T00:00:00Z"
    }
  ]
}
```

**Create News:** `POST /admin/berita`

**Request (multipart/form-data):**
```
title: "Judul Berita"
content: "Isi berita panjang..."
author: "Admin Name"
status: "published" // or "draft"
image: [File]
```

**Edit News:** `PUT /admin/berita/{news_id}`

**Delete News:** `DELETE /admin/berita/{news_id}`

### 8.2 Gallery Management

**List Endpoint:** `GET /admin/galeri`

**Response:**
```json
{
  "success": true,
  "galleries": [
    {
      "id": 12,
      "title": "Interior Restoran",
      "image": "https://tastyfood.com/storage/galleries/interior-1.jpg",
      "description": "Suasana nyaman...",
      "category": "interior",
      "status": "active"
    }
  ]
}
```

**Upload Gallery:** `POST /admin/galeri`

**Request (multipart/form-data):**
```
title: "Foto Makanan"
description: "Optional description"
category: "food"
status: "active"
image: [File]
```

**Delete Gallery:** `DELETE /admin/galeri/{gallery_id}`

### 8.3 Contact Messages

**List Endpoint:** `GET /admin/pesan`

**Response:**
```json
{
  "success": true,
  "messages": [
    {
      "id": 8,
      "subject": "Pertanyaan Catering",
      "name": "Rina Wijaya",
      "email": "rina@example.com",
      "message": "Apakah menerima pesanan catering...",
      "is_read": false,
      "created_at": "2026-10-13T03:00:00Z"
    }
  ]
}
```

**Mark as Read:** `PATCH /admin/pesan/{message_id}`

**Request:**
```json
{
  "is_read": true
}
```

**Delete Message:** `DELETE /admin/pesan/{message_id}`

---

## 9. SETTINGS & CONFIGURATION

### 9.1 Get Settings

**Endpoint:** `GET /admin/pengaturan`

**Response:**
```json
{
  "success": true,
  "settings": {
    "restaurant_name": "Padang Rice",
    "restaurant_address": "Jl. Sudirman No. 45, Jakarta",
    "restaurant_phone": "021-12345678",
    "restaurant_email": "info@padangrice.com",
    "restaurant_latitude": "-6.200000",
    "restaurant_longitude": "106.816666",
    "opening_hours": "08:00 - 22:00",
    "delivery_base_fee": "5000",
    "delivery_per_km": "2000",
    "max_delivery_radius": "10"
  }
}
```

### 9.2 Update Settings

**Endpoint:** `POST /admin/pengaturan`

**Request:**
```json
{
  "restaurant_phone": "021-87654321",
  "opening_hours": "09:00 - 23:00"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Pengaturan berhasil diperbarui"
}
```

**Mobile UI:**
- Group settings by category (Restaurant Info, Delivery, System)
- Use appropriate input types (text, number, map picker for location)
- Validate before submit
- Show success feedback

### 9.3 Update Restaurant Location

**Flow:**
1. Show current location on map
2. Allow dragging marker to new position
3. Reverse geocode to get address
4. Confirm and submit

**Implementation:**
```javascript
function LocationPicker({ initialLocation, onSave }) {
  const [marker, setMarker] = useState(initialLocation);
  
  return (
    <>
      <MapView
        initialRegion={{
          latitude: marker.latitude,
          longitude: marker.longitude,
          latitudeDelta: 0.01,
          longitudeDelta: 0.01
        }}
      >
        <Marker
          coordinate={marker}
          draggable
          onDragEnd={(e) => setMarker(e.nativeEvent.coordinate)}
        />
      </MapView>
      <Button
        title="Simpan Lokasi"
        onPress={() => onSave(marker)}
      />
    </>
  );
}
```

---

## 10. PUSH NOTIFICATIONS

### 10.1 Notification Types

**Priority Levels:**

1. **Critical (Sound + Vibration):**
   - New order received
   - New payment proof uploaded
   - Customer complaint in chat

2. **High (Vibration):**
   - Order cancelled by customer
   - Payment rejected by system (expired)

3. **Normal (Silent):**
   - New contact message
   - Daily sales report

### 10.2 FCM/APNs Integration

**Device Token Registration:**

**Endpoint:** `POST /api/admin/register-device`

**Request:**
```json
{
  "device_token": "fcm_token_here",
  "platform": "ios", // or "android"
  "app_version": "1.0.0"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Device registered"
}
```

### 10.3 Notification Payload Structure

**New Order:**
```json
{
  "title": "Pesanan Baru",
  "body": "Pesanan ORD-20261013045500 dari Budi Santoso - Rp 125.000",
  "data": {
    "type": "new_order",
    "order_id": 12,
    "order_number": "ORD-20261013045500",
    "amount": 125000
  },
  "sound": "new_order.mp3",
  "badge": 1
}
```

**Payment Verification:**
```json
{
  "title": "Bukti Pembayaran Baru",
  "body": "Pesanan ORD-20261013045500 menunggu verifikasi",
  "data": {
    "type": "payment_verification",
    "payment_id": 8,
    "order_id": 12
  },
  "sound": "payment.mp3",
  "badge": 1
}
```

**Chat Message:**
```json
{
  "title": "Pesan Baru dari Budi Santoso",
  "body": "Pesanan saya sudah sampai mana?",
  "data": {
    "type": "chat_message",
    "conversation_id": 3,
    "message_id": 45
  },
  "badge": 1
}
```

### 10.4 Notification Handling

**Foreground (App Active):**
```javascript
messaging().onMessage(async remoteMessage => {
  // Show in-app notification banner
  showInAppNotification(remoteMessage);
  
  // Update relevant screen if visible
  if (remoteMessage.data.type === 'new_order') {
    refreshOrderList();
  }
});
```

**Background/Quit (App Closed):**
```javascript
messaging().setBackgroundMessageHandler(async remoteMessage => {
  // System displays notification automatically
  // Store notification for later processing
});

messaging().onNotificationOpenedApp(remoteMessage => {
  // User tapped notification, app opened from background
  navigateToScreen(remoteMessage.data);
});

messaging().getInitialNotification().then(remoteMessage => {
  // App opened from quit state via notification
  if (remoteMessage) {
    navigateToScreen(remoteMessage.data);
  }
});
```

### 10.5 Navigation from Notifications

```javascript
function navigateToScreen(data) {
  switch (data.type) {
    case 'new_order':
      navigation.navigate('OrderDetail', { orderId: data.order_id });
      break;
    case 'payment_verification':
      navigation.navigate('PaymentVerification', { paymentId: data.payment_id });
      break;
    case 'chat_message':
      navigation.navigate('ChatMessages', { conversationId: data.conversation_id });
      break;
    default:
      navigation.navigate('Dashboard');
  }
}
```

---

## 11. OFFLINE CAPABILITIES

### 11.1 Offline Strategy

**What Works Offline:**
- View cached order list (last synced)
- View cached menu list
- View cached chat messages
- View dashboard stats (last synced)

**What Requires Connection:**
- Approve/reject payments (critical action)
- Send chat messages
- Update order status
- Upload images

### 11.2 Offline Indicator

**UI Component:**
```javascript
function OfflineBanner() {
  const isOnline = useNetworkState();
  
  if (isOnline) return null;
  
  return (
    <View style={styles.offlineBanner}>
      <Icon name="wifi-off" />
      <Text>Tidak ada koneksi internet</Text>
    </View>
  );
}
```

### 11.3 Queue Failed Actions

**Example: Order Status Update**
```javascript
async function updateOrderStatus(orderId, status) {
  try {
    await api.patch(`/admin/pesanan/${orderId}/status`, { status });
    return { success: true };
  } catch (error) {
    if (!isOnline()) {
      // Queue for retry when online
      await queueAction({
        type: 'update_order_status',
        orderId,
        status,
        timestamp: Date.now()
      });
      
      showToast('Aksi disimpan, akan diproses saat online');
      return { success: false, queued: true };
    }
    
    throw error;
  }
}
```

### 11.4 Sync on Reconnect

```javascript
NetInfo.addEventListener(state => {
  if (state.isConnected && previousState === false) {
    // Network restored
    processQueuedActions();
    refreshAllData();
    showToast('Koneksi pulih, data diperbarui');
  }
  
  previousState = state.isConnected;
});
```

---

## 12. API ENDPOINTS REFERENCE

### 12.1 Base URL

**Production:** `https://tastyfood.com`  
**Staging:** `https://staging.tastyfood.com`  
**Local Dev:** `http://localhost:8000`

### 12.2 Authentication Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/admin/login` | Admin login |
| POST | `/admin/logout` | Admin logout |
| GET | `/admin` | Validate token + dashboard |

### 12.3 Order Management Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/pesanan` | List orders (with filters) |
| GET | `/admin/pesanan/{id}` | Order detail |
| PATCH | `/admin/pesanan/{id}/status` | Update order status |

### 12.4 Payment Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/admin/order-management/payment/{id}/approve` | Approve payment |
| POST | `/admin/order-management/payment/{id}/reject` | Reject payment |

### 12.5 Chat Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/chat` | List conversations |
| GET | `/admin/chat/{id}` | Conversation detail |
| GET | `/admin/chat/{id}/messages` | Chat messages |
| POST | `/admin/chat/{id}/messages` | Send message |
| POST | `/admin/chat/{id}/read` | Mark as read |
| POST | `/admin/chat/{id}/typing` | Typing indicator |
| POST | `/admin/chat/{id}/close` | Close conversation |
| GET | `/admin/chat-unread` | Unread count |

### 12.6 Menu Management Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/menus` | List menus |
| POST | `/admin/menus` | Create menu |
| GET | `/admin/menus/{id}` | Menu detail |
| PUT | `/admin/menus/{id}` | Update menu |
| PATCH | `/admin/menus/{id}` | Partial update (e.g., availability) |
| DELETE | `/admin/menus/{id}` | Delete menu |

### 12.7 Content Management Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/berita` | List news |
| POST | `/admin/berita` | Create news |
| PUT | `/admin/berita/{id}` | Update news |
| DELETE | `/admin/berita/{id}` | Delete news |
| GET | `/admin/galeri` | List galleries |
| POST | `/admin/galeri` | Upload gallery |
| DELETE | `/admin/galeri/{id}` | Delete gallery |
| GET | `/admin/pesan` | List messages |
| PATCH | `/admin/pesan/{id}` | Mark message as read |
| DELETE | `/admin/pesan/{id}` | Delete message |

### 12.8 Settings Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/pengaturan` | Get settings |
| POST | `/admin/pengaturan` | Update settings |

### 12.9 Payment Methods Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/payment-methods` | List payment methods |
| POST | `/admin/payment-methods` | Create payment method |
| PUT | `/admin/payment-methods/{id}` | Update payment method |
| DELETE | `/admin/payment-methods/{id}` | Delete payment method |

---

## 13. DATA MODELS

### 13.1 Order Model

```typescript
interface Order {
  id: number;
  user_id: number | null;
  order_number: string; // e.g., "ORD-20261013045500"
  customer_name: string;
  customer_email: string;
  customer_phone: string;
  delivery_method: 'pickup' | 'delivery';
  delivery_address: string | null;
  latitude: number | null;
  longitude: number | null;
  status: 'pending' | 'confirmed' | 'preparing' | 'ready' | 'in_transit' | 'delivered' | 'completed' | 'cancelled';
  subtotal: number;
  delivery_fee: number;
  total: number;
  notes: string | null;
  created_at: string; // ISO 8601
  updated_at: string;
  items: OrderItem[];
  payment: Payment | null;
  user: User | null;
}
```

### 13.2 OrderItem Model

```typescript
interface OrderItem {
  id: number;
  order_id: number;
  menu_id: number | null;
  menu_name: string;
  menu_price: number;
  menu_image: string | null;
  quantity: number;
  subtotal: number;
}
```

### 13.3 Payment Model

```typescript
interface Payment {
  id: number;
  order_id: number;
  payment_method_id: number | null;
  user_id: number | null;
  amount: number;
  status: 'unpaid' | 'waiting_verification' | 'paid' | 'rejected' | 'expired';
  proof_image: string | null;
  user_notes: string | null;
  rejection_reason: string | null;
  verified_by: number | null;
  verified_at: string | null;
  created_at: string;
  payment_method: PaymentMethod | null;
}
```

### 13.4 PaymentMethod Model

```typescript
interface PaymentMethod {
  id: number;
  name: string;
  type: 'bank_transfer' | 'qr_payment' | 'e_wallet' | 'cash';
  account_name: string | null;
  account_number: string | null;
  qr_code: string | null;
  instructions: string | null;
  is_active: boolean;
}
```

### 13.5 Conversation Model

```typescript
interface Conversation {
  id: number;
  user_id: number;
  order_id: number | null;
  order_number: string | null;
  status: 'open' | 'closed';
  last_message: string | null;
  last_message_at: string | null;
  unread_count: number;
  user: User;
}
```

### 13.6 ChatMessage Model

```typescript
interface ChatMessage {
  id: number;
  uuid: string;
  conversation_id: number;
  sender_type: 'user' | 'admin' | 'system';
  sender_id: number;
  message: string;
  message_type: 'text' | 'system';
  is_read: boolean;
  read_at: string | null;
  created_at: string;
}
```

### 13.7 Menu Model

```typescript
interface Menu {
  id: number;
  name: string;
  description: string | null;
  price: number;
  image: string | null;
  category: 'nasi' | 'lauk' | 'sayur' | 'minuman';
  available: boolean;
  order_count: number;
  sort_order: number;
  created_at: string;
}
```

### 13.8 Admin Model

```typescript
interface Admin {
  id: number;
  name: string;
  email: string;
  created_at: string;
}
```

### 13.9 User Model

```typescript
interface User {
  id: number;
  name: string;
  email: string;
  phone: string | null;
  role: string; // 'customer' by default
  created_at: string;
}
```

---

## 14. UI/UX GUIDELINES

### 14.1 Design System

**Color Palette:**
```javascript
const colors = {
  primary: '#1F2937', // Black/Dark Gray
  primaryDark: '#111827',
  accent: '#EAB308', // Yellow
  accentLight: '#FEF3C7',
  success: '#10B981',
  warning: '#F59E0B',
  error: '#EF4444',
  info: '#3B82F6',
  
  background: '#FFFFFF',
  surface: '#F9FAFB',
  border: '#E5E7EB',
  
  textPrimary: '#111827',
  textSecondary: '#6B7280',
  textDisabled: '#9CA3AF',
};
```

**Typography:**
```javascript
const typography = {
  h1: { fontSize: 32, fontWeight: '700', lineHeight: 40 },
  h2: { fontSize: 24, fontWeight: '600', lineHeight: 32 },
  h3: { fontSize: 20, fontWeight: '600', lineHeight: 28 },
  body: { fontSize: 16, fontWeight: '400', lineHeight: 24 },
  bodyBold: { fontSize: 16, fontWeight: '600', lineHeight: 24 },
  caption: { fontSize: 14, fontWeight: '400', lineHeight: 20 },
  captionBold: { fontSize: 14, fontWeight: '600', lineHeight: 20 },
  small: { fontSize: 12, fontWeight: '400', lineHeight: 16 },
};
```

**Spacing:**
```javascript
const spacing = {
  xs: 4,
  sm: 8,
  md: 16,
  lg: 24,
  xl: 32,
  xxl: 48,
};
```

### 14.2 Navigation Structure

**Tab Navigation (Bottom):**
1. **Dashboard** (Home icon)
   - Stats overview
   - Quick actions

2. **Orders** (List icon)
   - Order list with filters
   - Badge showing pending count

3. **Chat** (Message icon)
   - Conversation inbox
   - Badge showing unread count

4. **More** (Menu icon)
   - Menu management
   - Content management
   - Settings
   - Logout

**Stack Navigation:**
- Order Detail
- Payment Verification
- Chat Messages
- Menu Create/Edit
- News Create/Edit
- Settings

### 14.3 Common UI Patterns

**Loading States:**
- Skeleton screens for lists
- Spinner for actions (button loading)
- Progress bar for uploads

**Empty States:**
- Friendly illustration
- Descriptive text
- Call-to-action button

**Error States:**
- Error icon
- Error message
- Retry button
- Support contact option

**Pull-to-Refresh:**
- All list screens
- Dashboard stats

**Infinite Scroll:**
- Order list
- Chat messages (load older)

### 14.4 Accessibility

**Must Implement:**
- Minimum touch target: 44x44 points
- Sufficient color contrast (WCAG AA)
- Screen reader labels (accessibilityLabel)
- Keyboard navigation (Android TV support)
- Dynamic text size support

**Testing:**
- iOS: VoiceOver
- Android: TalkBack

---

## 15. TESTING & QA

### 15.1 Unit Testing

**Test Coverage Goals:**
- Business logic functions: 80%+
- API request/response parsing: 100%
- Date/number formatting utilities: 100%

**Example (Jest + React Native):**
```javascript
describe('Order Status Utils', () => {
  test('getNextActions returns correct actions for preparing status', () => {
    const actions = getNextActions('preparing');
    expect(actions).toContain('Tandai Siap');
    expect(actions).not.toContain('Batalkan');
  });
  
  test('formatRupiah formats number correctly', () => {
    expect(formatRupiah(125000)).toBe('Rp 125.000');
  });
});
```

### 15.2 Integration Testing

**Critical Flows to Test:**
1. Login → Dashboard → Logout
2. View order → Update status → Verify in list
3. View payment → Approve → Verify order status changed
4. Open chat → Send message → Verify delivery
5. Toggle menu availability → Verify API call

**Tools:**
- Detox (React Native E2E)
- Appium (cross-platform)

### 15.3 Manual QA Checklist

**Authentication:**
- [ ] Login with valid credentials succeeds
- [ ] Login with invalid credentials fails with clear message
- [ ] Logout clears token and navigates to login
- [ ] Token validation on app launch works
- [ ] 401 responses trigger logout

**Orders:**
- [ ] Order list loads and displays correctly
- [ ] Filters work (status, payment_status, search)
- [ ] Order detail shows all information
- [ ] Map view displays for delivery orders
- [ ] Status update works and updates UI
- [ ] Status transition validation prevents invalid changes

**Payments:**
- [ ] Pending payments list loads
- [ ] Proof image displays full-screen with zoom
- [ ] Approve payment succeeds and removes from pending
- [ ] Reject payment requires reason
- [ ] Rejection reason is submitted correctly

**Chat:**
- [ ] Conversation list loads with unread badges
- [ ] Chat messages load in correct order
- [ ] Send message works and displays optimistically
- [ ] Typing indicator appears when customer types
- [ ] Unread count updates in real-time
- [ ] Close conversation works

**Menu:**
- [ ] Menu list loads with images
- [ ] Toggle availability works instantly
- [ ] Create menu with image upload succeeds
- [ ] Edit menu updates correctly
- [ ] Delete menu removes from list

**Notifications:**
- [ ] Push notifications received when app closed
- [ ] Notification tap navigates to correct screen
- [ ] In-app banner shows for foreground notifications
- [ ] Badge count updates correctly

**Offline:**
- [ ] Offline banner appears when disconnected
- [ ] Cached data displays when offline
- [ ] Actions requiring connection show error
- [ ] Queued actions execute when reconnected

**Performance:**
- [ ] App launches in < 3 seconds
- [ ] List scrolling is smooth (60fps)
- [ ] Image loading uses placeholders
- [ ] No memory leaks on long usage

**Edge Cases:**
- [ ] Empty states display correctly
- [ ] Error messages are user-friendly
- [ ] Long text truncates properly
- [ ] Large images don't crash app
- [ ] Rapid tapping doesn't duplicate actions

### 15.4 Device Testing Matrix

**iOS:**
- iPhone SE (small screen)
- iPhone 14 Pro (notch)
- iPad (tablet layout)
- iOS 16+

**Android:**
- Small phone (5" screen)
- Large phone (6.5"+ screen)
- Tablet (10")
- Android 10+

**Network Conditions:**
- WiFi (fast)
- 4G (medium)
- 3G (slow)
- Offline

---

## APPENDIX A: SAMPLE API RESPONSES

### Dashboard API Response (Full)
```json
{
  "success": true,
  "stats": {
    "total_news": 12,
    "total_galleries": 45,
    "unread_messages": 3,
    "pending_orders": 5,
    "pending_payments": 2,
    "today_orders": 23,
    "today_revenue": 2850000,
    "active_chats": 4
  },
  "recentOrders": [
    {
      "id": 12,
      "order_number": "ORD-20261013045500",
      "customer_name": "Budi Santoso",
      "total": 125000,
      "status": "preparing",
      "created_at": "2026-10-13T04:55:00Z"
    }
  ],
  "recentNews": [
    {
      "id": 1,
      "title": "Promo Ramadan 2026",
      "image": "https://tastyfood.com/storage/news/promo.jpg",
      "created_at_human": "2 jam yang lalu"
    }
  ],
  "recentMessages": [
    {
      "id": 5,
      "name": "John Doe",
      "subject": "Pertanyaan tentang delivery",
      "is_read": false,
      "created_at_human": "5 menit yang lalu"
    }
  ],
  "pendingPayments": [
    {
      "id": 8,
      "order_number": "ORD-20261013045500",
      "amount": 125000,
      "proof_image": "https://tastyfood.com/storage/payments/proof_8.jpg",
      "created_at": "2026-10-13T04:58:00Z"
    }
  ]
}
```

---

## APPENDIX B: ERROR HANDLING

### Standard Error Response Format
```json
{
  "success": false,
  "message": "User-friendly error message",
  "errors": {
    "field_name": ["Validation error 1", "Validation error 2"]
  },
  "code": "ERROR_CODE"
}
```

### Common Error Codes
- `UNAUTHENTICATED`: 401, token invalid/expired
- `FORBIDDEN`: 403, insufficient permissions
- `NOT_FOUND`: 404, resource doesn't exist
- `VALIDATION_ERROR`: 422, input validation failed
- `SERVER_ERROR`: 500, unexpected server error

### Client-side Error Handling
```javascript
async function apiRequest(endpoint, options) {
  try {
    const response = await axios.request({
      url: endpoint,
      ...options,
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json',
        ...options.headers
      }
    });
    
    return response.data;
  } catch (error) {
    if (error.response) {
      // Server responded with error
      const { status, data } = error.response;
      
      if (status === 401) {
        // Token expired, force logout
        await logout();
        throw new Error('Sesi berakhir, silakan login kembali');
      }
      
      if (status === 422) {
        // Validation error
        throw new ValidationError(data.errors);
      }
      
      throw new Error(data.message || 'Terjadi kesalahan');
    } else if (error.request) {
      // Network error
      throw new Error('Tidak dapat terhubung ke server');
    } else {
      // Unknown error
      throw new Error('Terjadi kesalahan tidak terduga');
    }
  }
}
```

---

## APPENDIX C: DEPLOYMENT CHECKLIST

**Before Release:**
- [ ] All API endpoints tested
- [ ] Push notifications configured (FCM/APNs)
- [ ] App icons & splash screens added
- [ ] Privacy policy & terms integrated
- [ ] Error tracking setup (Sentry/Crashlytics)
- [ ] Analytics setup (Firebase/Mixpanel)
- [ ] Production API URL configured
- [ ] Code signing certificates ready
- [ ] App store screenshots prepared
- [ ] Release notes written

**iOS Specific:**
- [ ] Bundle ID registered
- [ ] Provisioning profiles created
- [ ] Info.plist permissions configured
- [ ] TestFlight beta testing

**Android Specific:**
- [ ] App signing key generated
- [ ] Package name registered
- [ ] AndroidManifest.xml permissions configured
- [ ] Google Play internal testing

---

## CONCLUSION

This specification provides a complete blueprint for building a mobile admin application for Padang Rice restaurant management system. The app focuses on core operational tasks that benefit from mobile access: order management, payment verification, and customer support via chat.

**Key Priorities:**
1. Real-time order & payment notifications
2. Fast payment verification workflow
3. Responsive customer support via chat
4. Offline-capable order viewing

**Success Metrics:**
- Average payment approval time < 2 minutes
- Chat response time < 5 minutes
- Order status update within 30 seconds
- App crash rate < 0.1%

**Next Steps:**
1. Set up development environment
2. Implement authentication & token storage
3. Build dashboard with core metrics
4. Implement order management
5. Add payment verification
6. Integrate real-time chat
7. Beta testing with restaurant staff
8. Production deployment

---

**Document Version**: 1.0  
**Last Updated**: October 13, 2026  
**Maintained By**: Development Team  
**Contact**: tech@padangrice.com
