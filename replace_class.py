import os
import re

directory = r"d:\htdocs\CLSU\resources"

# Regex to match whole word 'engineering-page'
pattern = re.compile(r'\bengineering-page\b')

count = 0

for root, dirs, files in os.walk(directory):
    for file in files:
        if file.endswith(('.blade.php', '.css')):
            filepath = os.path.join(root, file)
            try:
                with open(filepath, 'r', encoding='utf-8') as f:
                    content = f.read()
                
                if pattern.search(content):
                    new_content = pattern.sub('college-page', content)
                    with open(filepath, 'w', encoding='utf-8') as f:
                        f.write(new_content)
                    print(f"Updated: {filepath}")
                    count += 1
            except Exception as e:
                print(f"Error reading {filepath}: {e}")

print(f"Total files updated: {count}")
