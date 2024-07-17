import pymysql
import json
from opencage.geocoder import OpenCageGeocode

api_key = 'efd17e46c9324adfbd1f7d1e04995748'  # Clé API OpenCage

def geocode_address(address, api_key):
    geocoder = OpenCageGeocode(api_key)
    try:
        result = geocoder.geocode(address)
        if result and len(result) > 0:
            first_result = result[0]
            return {
                'lat': first_result['geometry']['lat'],
                'long': first_result['geometry']['lng'],
                'formatted': first_result['formatted'],
            }
        else:
            return None
    except Exception as e:
        print(f'Error geocoding address: {e}')
        return None

def fetch_data_from_table(table, api_key, conn):
    data = []
    sql = f"SELECT * FROM {table}"
    try:
        with conn.cursor(pymysql.cursors.DictCursor) as cursor:
            cursor.execute(sql)
            results = cursor.fetchall()
            for row in results:
                address = f"{row['rue']}, {row['code_postal']} {row['ville']}"
                geocoded_data = geocode_address(address, api_key)
                if geocoded_data:
                    row['lat'] = geocoded_data['lat']
                    row['long'] = geocoded_data['long']
                    row['formatted'] = geocoded_data['formatted']
                data.append(row)
    except Exception as e:
        print(f"Error fetching data: {e}")
    return data

# Configuration de la connexion MySQL
servername = "127.0.0.1"
username = "root"
password = ""
dbname = "lions_pub_nikita"

try:
    conn = pymysql.connect(
        host=servername,
        user=username,
        password=password,
        db=dbname,
        charset='utf8mb4',
        cursorclass=pymysql.cursors.DictCursor
    )

    results = {}

    tables = ['societe_info_competitor', 'societe_info_vape_competitor', 'societe_info_gameBar_competitor']
    for table in tables:
        table_data = fetch_data_from_table(table, api_key, conn)
        if table == 'societe_info_competitor':
            results['results_societe_info_competitor'] = table_data
        elif table == 'societe_info_vape_competitor':
            results['results_societe_info_vape_competitor'] = table_data
        elif table == 'societe_info_gameBar_competitor':
            results['results_societe_info_gameBar_competitor'] = table_data

    if results:
        print(json.dumps({'success': True, 'data': results}, ensure_ascii=False))
    else:
        print(json.dumps({'success': False, 'error': 'Aucune donnée trouvée pour les tables spécifiées.'}, ensure_ascii=False))

finally:
    conn.close()
