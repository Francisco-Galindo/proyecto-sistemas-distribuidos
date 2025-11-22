from flask import Flask, Blueprint, request, jsonify
import psycopg2
from psycopg2 import extras
import os
 
app = Flask(__name__)

# --- Configuración de Conexión a BD ---
DB_HOST = os.environ.get('DB_HOST', 'postgres') 
DB_NAME = os.environ.get('POSTGRES_DB', 'chatdb')
DB_USER = os.environ.get('POSTGRES_USER', 'appuser')
DB_PASSWORD = os.environ.get('POSTGRES_PASSWORD', 'secretpassword')

def get_db_connection():
    """Establece y retorna una conexión a la base de datos PostgreSQL."""
    try:
        conn = psycopg2.connect(
            host=DB_HOST,
            database=DB_NAME,
            user=DB_USER,
            password=DB_PASSWORD
        )
        return conn
    except psycopg2.OperationalError as e:
        print(f"ERROR: No se pudo conectar a la base de datos. Detalles: {e}")
        return None

# --- Blueprint para rutas de contactos ---
contact_bp = Blueprint('contact_bp', __name__)

# ------------------------------
# Ruta: Agregar contacto
# ------------------------------
@app.route('/add_contact', methods=['POST'])
def add_contact():
    """
    Agrega un contacto usando el username del usuario.
    Se evita agregar duplicados o agregarse a sí mismo.
    """
    conn = get_db_connection()
    if conn is None:
        return jsonify({"message": "Error interno de conexión a la BD"}), 500

    data = request.get_json(silent=True) or request.form
    user_id = data.get("user_id")
    contact_username = data.get("contact_username")

    if not user_id or not contact_username:
        return jsonify({"message": "Faltan user_id o contact_username"}), 400

    cursor = conn.cursor()

    # Validar que user_id sea un entero
    try:
        user_id = int(user_id)
    except ValueError:
        return jsonify({"message": "El user_id debe ser un número entero."}), 400

    try:
        # 1. Buscar ID del contacto por username en la tabla 'usuarios'
        query_find_id = "SELECT id FROM usuarios WHERE username = %s;"
        cursor.execute(query_find_id, (contact_username,))
        contact_record = cursor.fetchone()
        
        if contact_record is None:
            return jsonify({"message": f"Usuario '{contact_username}' no registrado."}), 404
            
        contact_id = contact_record[0]

        # Evitar que el usuario se agregue a sí mismo
        if user_id == contact_id:
            return jsonify({"message": "No puedes agregarte a ti mismo como contacto."}), 400

        # 2. Insertar la relación en la tabla 'contactos'
        query_insert = """
        INSERT INTO contactos (user_id, contact_id)
        VALUES (%s, %s)
        ON CONFLICT (user_id, contact_id) DO NOTHING
        RETURNING id;
        """
        cursor.execute(query_insert, (user_id, contact_id))
        result = cursor.fetchone()
        conn.commit()

        if result:
            return jsonify({"message": f"{contact_username} se ha agregado como amigo."}), 201
        else:
            return jsonify({"message": f"{contact_username} ya estaba en tu lista de amigos."}), 200

    except Exception as e:
        conn.rollback()
        print("Error al agregar contacto:", e)
        return jsonify({"message": "Error interno al agregar contacto", "detalles": str(e)}), 500

    finally:
        cursor.close()
        conn.close()


# ------------------------------
# Ruta: Listar contactos del usuario
# ------------------------------
@app.route('/my_contacts', methods=['GET'])
def list_my_contacts():
    """
    Devuelve la lista de contactos de un usuario por su user_id.
    """
    conn = get_db_connection()
    if conn is None:
        return jsonify({"message": "Error interno de conexión a la BD."}), 500
    
    user_id = request.args.get('user_id', type=int)
    if not user_id:
        return jsonify({"message": "Se requiere el ID del usuario actual (user_id)."}), 400

    cursor = conn.cursor(cursor_factory=extras.RealDictCursor)

    try:
        query = """
        SELECT u.id, u.username
        FROM contactos c
        JOIN usuarios u ON c.contact_id = u.id
        WHERE c.user_id = %s
        ORDER BY u.username ASC;
        """
        cursor.execute(query, (user_id,))
        contacts = cursor.fetchall()

        return jsonify({"contacts": contacts}), 200

    except Exception as e:
        print(f"Error al listar mis contactos: {e}")
        return jsonify({"message": "Error interno al cargar la lista de contactos.", "error_detail": str(e)}), 500

    finally:
        cursor.close()
        conn.close()

if __name__ == '__main__':
    app.run(debug=True)

