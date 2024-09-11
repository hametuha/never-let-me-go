# 1. Load hash value from .wp_install_path or exit if not found.
if [ ! -f .wp_install_path ]; then
  echo ".wp_install_path not found. Skip mailhog installation."
  exit 0
fi
hash_value=$(cat .wp_install_path)

# 2. Replace %NETWORK_NAME% in compose.template.yaml
sed "s/%NETWORK_NAME%/${hash_value}/g" compose.template.yaml > compose.yaml

# 3. Display hash.
echo "Created compose.yaml with Hash: ${hash_value}"

docker compose up -d

echo "mailhog available at http://localhost:8025"
