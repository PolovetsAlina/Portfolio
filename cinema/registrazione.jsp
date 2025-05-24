<%@ page language="java" contentType="text/html; charset=ISO-8859-1" pageEncoding="ISO-8859-1"%>
<%@ page import="java.sql.*" %>
<%@ page import="javax.servlet.http.HttpSession" %> 
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione - Cinema Hub</title>
    <link rel="stylesheet" href="registrazione.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script>
       function toggleForm(formType) {
            if (formType === 'login') {
                document.getElementById('register-form').style.display = 'none';
                document.getElementById('login-form').style.display = 'block';
            } else {
                document.getElementById('login-form').style.display = 'none';
                document.getElementById('register-form').style.display = 'block';
            }
        }
    </script>
</head>
<body>
    <div class="wrapper">
        <form id="register-form" action="" method="post">
            <h1>Registrazione</h1>
            <div class="input-box">
                <input type="text" name="username" placeholder="Username" required>
                <i class="bx bx-user"></i>
            </div>

            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
                <i class="bx bxs-lock-alt"></i>
            </div>

            <div class="input-box">
                <input type="text" name="cognome" placeholder="Cognome" required>
            </div>

            <div class="input-box">
                <input type="text" name="nome" placeholder="Nome" required>
            </div>

            <div class="input-box">
                <input type="text" name="telefono" placeholder="Telefono" required>
            </div>

            <div class="input-box">
                <input type="text" name="codice_fiscale" placeholder="Codice Fiscale" required>
            </div>

            <button type="submit" class="btn">Registrati</button>
        </form>
    </div>

 <%
    String url = "jdbc:mysql://localhost:3306/cinema";
    String user = "root";
    String password = "password";
    Connection conn = null;
    PreparedStatement stmt = null;

    if ("POST".equalsIgnoreCase(request.getMethod())) {
        String action = request.getParameter("action");
        if (action == null) action = "register"; 

        if ("register".equals(action)) {
            String username = request.getParameter("username");
            String userPassword = request.getParameter("password");
            String cognome = request.getParameter("cognome");
            String nome = request.getParameter("nome");
            String telefono = request.getParameter("telefono");
            String codiceFiscale = request.getParameter("codice_fiscale"); 

            try {
                Class.forName("com.mysql.cj.jdbc.Driver");
                conn = DriverManager.getConnection(url, user, password);
                String sql = "INSERT INTO utenti (Username, Password, Cognome, Nome, Telefono, Codice_fiscale) VALUES (?,?,?,?,?,?)";
                stmt = conn.prepareStatement(sql);
                stmt.setString(1, username);
                stmt.setString(2, userPassword);
                stmt.setString(3, cognome);
                stmt.setString(4, nome);
                stmt.setString(5, telefono);
                stmt.setString(6, codiceFiscale); 

                int rowsInserted = stmt.executeUpdate();
                if (rowsInserted > 0) {
                    out.println("<script>alert('Registrazione completata con successo! Puoi accedere ora.'); window.location.href='index.jsp';</script>");
                } else {
                    out.println("<script>alert('Errore durante la registrazione. Riprova.');</script>");
                }
            } catch (Exception e) {
                e.printStackTrace();
                out.println("<script>alert('Errore nel database. Riprova.');</script>");
            } finally {
                try {
                    if (stmt != null) stmt.close();
                    if (conn != null) conn.close();
                } catch (SQLException e) {
                    e.printStackTrace();
                }
            }
        }
    }
%>

</body>
</html>

