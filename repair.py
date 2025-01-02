import os
import re

# Конфликтные маркеры Git
conflict_markers = ["<<<<<<<", "=======", ">>>>>>>"]

def remove_git_conflict_markers_and_duplicates(directory):
    for root, _, files in os.walk(directory):
        for file in files:
            file_path = os.path.join(root, file)
            
            try:
                # Пропускаем двоичные файлы
                with open(file_path, 'r', encoding='utf-8') as f:
                    content = f.read()
                
                # Удаляем конфликтные маркеры
                lines = content.splitlines()
                lines_without_conflicts = [
                    line for line in lines if not any(marker in line for marker in conflict_markers)
                ]
                
                # Соединяем строки после удаления маркеров
                cleaned_content = "\n".join(lines_without_conflicts)

                # Регулярное выражение для выделения блоков PHP-кода
                php_block_pattern = re.compile(r'<\?php[\s\S]*?\?>', re.MULTILINE)

                # Находим все блоки PHP-кода
                php_blocks = php_block_pattern.findall(cleaned_content)

                # Удаляем дубликаты, сохраняя порядок
                unique_php_blocks = list(dict.fromkeys(php_blocks))

                # Остальная часть файла (вне PHP-блоков)
                non_php_content = php_block_pattern.sub("", cleaned_content).strip()

                # Соединяем уникальные блоки PHP-кода и не-PHP контент
                final_content = "\n".join(unique_php_blocks) + "\n\n" + non_php_content

                # Если файл изменён, сохраняем его
                if content != final_content:
                    with open(file_path, 'w', encoding='utf-8') as f:
                        f.write(final_content)
                    print(f"Обработан файл: {file_path}")
            except Exception as e:
                print(f"Не удалось обработать файл {file_path}: {e}")

if __name__ == "__main__":
    directory_to_check = "./"
    remove_git_conflict_markers_and_duplicates(directory_to_check)
