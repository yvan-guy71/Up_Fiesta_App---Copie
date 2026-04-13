import 'package:sqflite/sqflite.dart';
import 'package:path/path.dart';
import 'dart:convert';

class DatabaseService {
  static final DatabaseService _instance = DatabaseService._internal();
  factory DatabaseService() => _instance;
  DatabaseService._internal();

  Database? _database;

  Future<Database> get database async {
    if (_database != null) return _database!;
    _database = await _initDatabase();
    return _database!;
  }

  Future<Database> _initDatabase() async {
    String path = join(await getDatabasesPath(), 'up_fiesta.db');
    return await openDatabase(
      path,
      version: 1,
      onCreate: (db, version) async {
        // Table pour mettre en cache les prestataires
        await db.execute('''
          CREATE TABLE providers(
            id INTEGER PRIMARY KEY,
            data TEXT,
            category_id INTEGER,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
          )
        ''');
        // Table pour mettre en cache les messages
        await db.execute('''
          CREATE TABLE messages(
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            other_user_id INTEGER,
            content TEXT,
            sender_id INTEGER,
            created_at TEXT
          )
        ''');
      },
    );
  }

  // Sauvegarder les prestataires en local
  Future<void> cacheProviders(List<Map<String, dynamic>> providers, {int? categoryId}) async {
    final db = await database;
    await db.transaction((txn) async {
      for (var provider in providers) {
        await txn.insert(
          'providers',
          {
            'id': provider['id'],
            'data': json.encode(provider),
            'category_id': categoryId ?? 0,
          },
          conflictAlgorithm: ConflictAlgorithm.replace,
        );
      }
    });
  }

  // Récupérer les prestataires du cache (Hors-ligne)
  Future<List<Map<String, dynamic>>> getCachedProviders({int? categoryId}) async {
    final db = await database;
    final List<Map<String, dynamic>> maps = await db.query(
      'providers',
      where: categoryId != null ? 'category_id = ?' : null,
      whereArgs: categoryId != null ? [categoryId] : null,
    );

    return List.generate(maps.length, (i) {
      return json.decode(maps[i]['data']);
    });
  }

  // Sauvegarder les messages en local
  Future<void> cacheMessages(int otherUserId, List<Map<String, dynamic>> messages) async {
    final db = await database;
    await db.delete('messages', where: 'other_user_id = ?', whereArgs: [otherUserId]);
    for (var msg in messages) {
      await db.insert('messages', {
        'other_user_id': otherUserId,
        'content': msg['content'],
        'sender_id': msg['sender_id'],
        'created_at': msg['created_at'],
      });
    }
  }

  Future<List<Map<String, dynamic>>> getCachedMessages(int otherUserId) async {
    final db = await database;
    return await db.query('messages', where: 'other_user_id = ?', whereArgs: [otherUserId], orderBy: 'created_at ASC');
  }
}
