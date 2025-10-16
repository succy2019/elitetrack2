import Database, { type Database as DatabaseType } from 'better-sqlite3'
import { User, Track, UserSettings, Admin } from '../types/database'
import path from 'path'

// Initialize database connection - always use in-memory for serverless
const dbPath = ':memory:'
const db = new Database(dbPath)

// Enable foreign keys
db.pragma('foreign_keys = ON')

// Create tables if they don't exist
db.exec(`
  CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email TEXT UNIQUE NOT NULL,
    name TEXT NOT NULL,
    amount TEXT NOT NULL,
    status TEXT NOT NULL,
    phone TEXT NOT NULL,
    address TEXT NOT NULL,
    message TEXT NOT NULL,
    track_id TEXT UNIQUE NOT NULL,
    payment_to TEXT NOT NULL DEFAULT 'Merchant Commercial Bank',
    account_number TEXT NOT NULL DEFAULT '0012239988',
    estimated_processing_time TEXT NOT NULL DEFAULT '1-2 minutes',
    money_due TEXT NOT NULL,
    progress_percentage INTEGER NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
  );

  CREATE TABLE IF NOT EXISTS admins (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
  );
`)

// ✅ Database client class
export class DbClient {
  private db: DatabaseType

  constructor() {
    // Always use in-memory database for serverless
    this.db = new Database(':memory:')
    this.db.pragma('foreign_keys = ON')
    
    // Always initialize tables for in-memory database
    this.initializeTables()
  }

  private initializeTables(): void {
    this.db.exec(`
      CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        email TEXT UNIQUE NOT NULL,
        name TEXT NOT NULL,
        amount TEXT NOT NULL,
        status TEXT NOT NULL,
        phone TEXT NOT NULL,
        address TEXT NOT NULL,
        message TEXT NOT NULL,
        track_id TEXT UNIQUE NOT NULL,
        payment_to TEXT NOT NULL DEFAULT 'Merchant Commercial Bank',
        account_number TEXT NOT NULL DEFAULT '0012239988',
        estimated_processing_time TEXT NOT NULL DEFAULT '1-2 minutes',
        money_due TEXT NOT NULL,
        progress_percentage INTEGER NOT NULL DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
      );

      CREATE TABLE IF NOT EXISTS admins (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        email TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
      );
    `)
  }

  // Generate unique track ID
  private generateTrackId(): string {
    const timestamp = Date.now().toString(36)
    const randomStr = Math.random().toString(36).substring(2, 8)
    return `TRK-${timestamp}-${randomStr}`.toUpperCase()
  }

  createUser(user: Omit<User, 'id' | 'created_at' | 'updated_at' | 'track_id'>): number {
    const trackId = this.generateTrackId()
    const query = this.db.prepare(
      `INSERT INTO users (email, name, amount, status, phone, address, message, track_id, payment_to, account_number, estimated_processing_time, money_due, progress_percentage) 
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`
    )
    const result = query.run(
      user.email,
      user.name,
      user.amount,
      user.status,
      user.phone,
      user.address,
      user.message,
      trackId,
      user.payment_to || 'Merchant Commercial Bank',
      user.account_number || '0012239988',
      user.estimated_processing_time || '1-2 minutes',
      user.money_due || user.amount,
      user.progress_percentage || 0
    )
    return Number(result.lastInsertRowid)
  }

  getUserById(id: number): User | null {
    const query = this.db.prepare('SELECT * FROM users WHERE id = ?')
    return query.get(id) as User | null
  }

  getUserByEmail(email: string): User | null {
    const query = this.db.prepare('SELECT * FROM users WHERE email = ?')
    return query.get(email) as User | null
  }

  getUserByTrackId(trackId: string): User | null {
    const query = this.db.prepare('SELECT * FROM users WHERE track_id = ?')
    return query.get(trackId) as User | null
  }


  deleteUser(id: number): void {
    const query = this.db.prepare('DELETE FROM users WHERE id = ?')
    query.run(id)
  }

  updateUserProfile(
    userId: number, 
    name: string, 
    email: string, 
    amount: string, 
    status: string, 
    message: string, 
    address: string, 
    phone: string,
    payment_to?: string,
    account_number?: string,
    estimated_processing_time?: string,
    money_due?: string,
    progress_percentage?: number
  ): User | null {
    const query = this.db.prepare(`
      UPDATE users 
      SET name = ?,
          email = ?,
          amount = ?,
          status = ?,
          message = ?,
          address = ?,
          phone = ?,
          payment_to = ?,
          account_number = ?,
          estimated_processing_time = ?,
          money_due = ?,
          progress_percentage = ?,
          updated_at = CURRENT_TIMESTAMP 
      WHERE id = ?
    `)

    query.run(
      name,
      email,
      amount,
      status,
      message,
      address,
      phone,
      payment_to || 'Merchant Commercial Bank',
      account_number || '0012239988',
      estimated_processing_time || '1-2 minutes',
      money_due || amount,
      progress_percentage || 0,
      userId
    )

    return this.getUserById(userId)
  }

  updateUserProgress(userId: number, progressPercentage: number): User | null {
    const query = this.db.prepare(`
      UPDATE users 
      SET progress_percentage = ?,
          updated_at = CURRENT_TIMESTAMP 
      WHERE id = ?
    `)
    query.run(progressPercentage, userId)
    return this.getUserById(userId)
  }

  updateAdminPassword(email: string, password: string): void {
    const query = this.db.prepare(`
      UPDATE admins 
      SET password = ?, updated_at = CURRENT_TIMESTAMP 
      WHERE email = ?
    `)
    query.run(password, email)
  }

  getAdminByEmail(email: string): Admin | null {
    const query = this.db.prepare('SELECT * FROM admins WHERE email = ?')
    return query.get(email) as Admin | null
  }

  getAllUsers(): User[] {
    const query = this.db.prepare('SELECT * FROM users ORDER BY created_at DESC')
    return query.all() as User[]
  }

  createAdmin(admin: { email: string; password: string }): number {
    const query = this.db.prepare(
      'INSERT INTO admins (email, password) VALUES (?, ?)'
    )
    const result = query.run(admin.email, admin.password)
    return Number(result.lastInsertRowid)
  }

}

// ✅ Export single shared instance
export const dbClient = new DbClient()
export default dbClient
