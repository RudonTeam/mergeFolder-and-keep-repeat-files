# mergeFolder-and-keep-repeat-files
Keep repeat files by name like logo(1).jpg in windows when copy / combine / merge folders in Mac or Linux. 


## DEMO
1. Download 'mergeFolders.php' to /any/folder/
2. Create 2 folders: /any/folder/from/ and /any/folder/to/
3. Copy every files or folders which are going to be merged into folder "from"
4. Enable PHP in your Mac or Linux [https://rudon.blog.csdn.net/article/details/104745975](https://rudon.blog.csdn.net/article/details/104745975)
5. Run the command below to merge the files in folder "from":<pre>php /any/folder/mergeFolders.php</pre>
6. OK! Check the files in folder "to".


## Topic
文件夹合并工具 for Mac， 实现复制文件时"保留两个文件"的效果


## Description
在Windows系统下从-文件夹A-复制文件到-文件夹B-时，
如果文件logo.jpg重复了，系统会在文件夹B里创建文件'logo(1).jpg'，
但是，Mac系统不会，只能"跳过"，"停止"，"替换"，这个简单的要求"保留"为什么不做出来？
本工具支持多层文件夹合并。


## How to use
1. 在桌面上新建两个文件夹，取名"from"和"to"，把你需要合并的文件夹、文件统统复制到文件夹"from"
2. 下载本文件"mergeFolders.php"到Macbook上的桌面
3. 打开命令行工具 （按F4 > 文件夹"其它" > 终端）
4. 在Mac中开启PHP语言支持： https://rudon.blog.csdn.net/article/details/104745975
5. 在命令行中，输入命令：php ~/Desktop/mergeFolders.php
6. OK, 稍等片刻，检查文件夹"to"是否包含了所有的文件
