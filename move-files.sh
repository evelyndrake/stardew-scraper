#!/bin/bash
destination="../cgi-bin"
cp -r * "$destination/"
#Add /~jcd171/ to all img tags
find "$destination" -type f -name "*.php" -exec sed -i 's/src=\'icons\//src=\'\/~jcd171\/icons\//g' {} +