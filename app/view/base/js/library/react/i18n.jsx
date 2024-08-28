import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';

i18n
    .use(initReactI18next)
    .init({
        resources: {
            en: {
                translation: {
                    // English translations
                },
            },
            ru: {
                translation: {
                    'Customer Login': 'Авторизация пользователя',
                    'Password': 'Пароль',
                    'Restore': 'Восстановить',
                    'Create User': 'Создать пользователя',
                    'Login': 'Войти',
                    'Your Sites': 'Ваши сайты',
                    'Account': 'Аккаунт',
                    'Email': 'Электронная почта',
                    'Logout': 'Выйти',
                    'Edit Customer': 'Редактировать пользователя',
                    'Name': 'Имя',
                    'Password Repeat': 'Повторить пароль',
                    'ACL': 'ACL',
                    'Customers': 'Пользователи',
                    'Save': 'Сохранить',
                    'Account Info': 'Информация аккаунта',
                    'Account Information': 'Информация аккаунта',
                    'Change Password': 'Изменить пароль',
                    'New Password': 'Новый пароль',
                    'Site Builder': 'Конструктор сайта',
                    'Edit': 'Редактировать',
                    'Current password is required': 'Текущий пароль обязателен',
                    'Name is required': 'Имя обязательно',
                    'Password is required': 'Пароль обязателен',
                    'Add New Site': 'Добавить новый сайт',
                    'Edit the Site': 'Редактировать сайт',
                    'Create a New Site': 'Создать новый сайт',
                    'Edit Site': 'Редактировать сайт',
                    'The logo should be 200px x 200px.': 'Логотип должен быть 200px x 200px.',
                    'The icon should be 200px x 200px.': 'Иконка должна быть 200px x 200px.',
                    'The logo has been uploaded.': 'Логотип загружен.',
                    'Drop logo here ...': 'Перетащите логотип сюда ...',
                    'Domain names': 'Доменные имена',
                    'Tagline': 'Слоган',
                    'Address': 'Адрес',
                    'Phone Number': 'Номер телефона:',
                    'Copyright': 'Авторское право',
                    'Create': 'Создать',
                    'Decline': 'Отклонить',
                    'This field is required': 'Это поле обязательно',
                    'Logo': 'Логотип',
                    'Icon': 'Иконка',
                    'The icon has been uploaded.': 'Иконка загружена.',
                    'General': 'Общее',
                    'SMTP': 'SMTP',
                    'SMTP Server': 'SMTP сервер',
                    'SMTP Port': 'SMTP порт',
                    'SMTP User': 'SMTP пользователь',
                    'SMTP Password': 'SMTP пароль',
                    'SMTP SSL/TLS': 'SMTP SSL/TLS',
                    'Sender Name': 'Имя отправителя',
                    'Sender Email': 'Email отправителя',
                    'Edit Site Configuration': 'Редактировать конфигурацию сайта',
                    'Site Customers': 'Пользователи сайта',
                    'Actions': 'Действия',
                    'Sites': 'Сайты',
                    'New Customer': 'Новый пользователь',
                    'Passwords do not match': 'Пароли не совпадают',
                    'Domain': 'Домен',
                    'Delete': 'Удалить',
                    'Add': 'Добавить',
                    'Language': 'Язык',
                    'Email address is required': 'Требуется адрес электронной почты',
                    'Invalid email address': 'Неверный email адрес',
                    'Phone number is required': 'Требуется номер телефона',
                    'Invalid phone number': 'Неверный номер телефона',
                    'Passwords should be at least 6 characters long': 'Пароли должны быть длиной не менее 6 символов',
                    'Restore Password': 'Восстановить пароль',
                },
            },
        },
        lng: 'en', // default language
        interpolation: {
            escapeValue: false, // react already safe from xss
        },
    });

export default i18n;
