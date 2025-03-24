#!/bin/bash
destination="../cgi-bin"
cp -r * "$destination/"
#Add /~jcd171/ to all img tags (src='icons/')
find "$destination" -type f -name "*.php" -exec sed -i "s/src='icons\//src='\/~jcd171\/icons\//g" {} +
#Add /~jcd171/ to all img tags (src="icons/")
find "$destination" -type f -name "*.php" -exec sed -i 's/src=\"icons\//src=\"\/~jcd171\/icons\//g' {} +
find "$destination" -type f -name "farming_tab.php" -exec sed -i 's/src=\"icons\//src=\"\/~jcd171\/icons\//g' {} +
#Replace $image_path = "icons/..." with $image_path = "/~jcd171/icons/..."
find "$destination" -type f -name "*.php" -exec sed -i 's,$image_path = "icons/,$image_path = "/~jcd171/icons/,g' {} +