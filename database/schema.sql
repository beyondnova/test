CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    role TEXT NOT NULL CHECK(role IN ('admin','staff')),
    active INTEGER NOT NULL DEFAULT 1,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS clients (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT,
    phone TEXT,
    id_number TEXT,
    address TEXT,
    notes TEXT,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS rooms (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    number TEXT NOT NULL UNIQUE,
    type TEXT NOT NULL CHECK(type IN ('single','double','suite','deluxe')),
    rate REAL NOT NULL DEFAULT 0,
    status TEXT NOT NULL DEFAULT 'available' CHECK(status IN ('available','occupied','maintenance')),
    description TEXT
);

CREATE TABLE IF NOT EXISTS bookings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    client_id INTEGER NOT NULL REFERENCES clients(id),
    room_id INTEGER NOT NULL REFERENCES rooms(id),
    check_in TEXT NOT NULL,
    check_out TEXT NOT NULL,
    nights INTEGER NOT NULL DEFAULT 1,
    rate REAL NOT NULL DEFAULT 0,
    status TEXT NOT NULL DEFAULT 'booked' CHECK(status IN ('booked','checked_in','checked_out','cancelled')),
    notes TEXT,
    created_by INTEGER REFERENCES users(id),
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS invoices (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    booking_id INTEGER NOT NULL UNIQUE REFERENCES bookings(id),
    room_charge REAL NOT NULL DEFAULT 0,
    extra_charges REAL NOT NULL DEFAULT 0,
    discount REAL NOT NULL DEFAULT 0,
    tax REAL NOT NULL DEFAULT 0,
    total REAL NOT NULL DEFAULT 0,
    paid_amount REAL NOT NULL DEFAULT 0,
    status TEXT NOT NULL DEFAULT 'pending' CHECK(status IN ('pending','partial','paid','cancelled')),
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS payments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    invoice_id INTEGER NOT NULL REFERENCES invoices(id),
    amount REAL NOT NULL,
    method TEXT NOT NULL CHECK(method IN ('cash','card','transfer','other')),
    reference TEXT,
    paid_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    recorded_by INTEGER REFERENCES users(id)
);

CREATE INDEX IF NOT EXISTS idx_bookings_client ON bookings(client_id);
CREATE INDEX IF NOT EXISTS idx_bookings_room ON bookings(room_id);
CREATE INDEX IF NOT EXISTS idx_bookings_status ON bookings(status);
CREATE INDEX IF NOT EXISTS idx_invoices_booking ON invoices(booking_id);
CREATE INDEX IF NOT EXISTS idx_payments_invoice ON payments(invoice_id);
