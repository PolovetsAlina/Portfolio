<%@ page language="java" contentType="text/html; charset=UTF-8" pageEncoding="UTF-8"%>
<%@ page import="java.sql.*" %>
<%
    request.setCharacterEncoding("UTF-8");
    
    String username = request.getParameter("username");
    String action = request.getParameter("action");
    String success = request.getParameter("success");
    String error = request.getParameter("error");
    
    if (username == null || username.isEmpty()) {
        response.sendRedirect("area_amministratore.jsp?error=Username non specificato");
        return;
    }

    Connection conn = null;
    PreparedStatement pstmt = null;
    PreparedStatement checkStmt = null;
    ResultSet rs = null;
    
    try {
        Class.forName("com.mysql.cj.jdbc.Driver");
        conn = DriverManager.getConnection("jdbc:mysql://localhost:3306/cinema", "root", "");
        
        if (action == null || !action.equals("update")) {
            String sql = "SELECT * FROM utenti WHERE username = ?";
            checkStmt = conn.prepareStatement(sql);
            checkStmt.setString(1, username);
            rs = checkStmt.executeQuery();
            
            if (!rs.next()) {
                response.sendRedirect("area_amministratore.jsp?error=Utente non trovato");
                return;
            }
%>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Modifica Utente - Cinema Hub</title>
    <link rel="stylesheet" href="index.css?v=<%= new java.util.Date().getTime() %>">
    <style>
        .form-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #1a1a1a;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
            color: white;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #e0e0e0;
        }
        input[type="text"], input[type="tel"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #333;
            border-radius: 4px;
            background-color: #2d2d2d;
            color: white;
        }
        input[type="text"]:focus, input[type="tel"]:focus {
            outline: none;
            border-color: #4CAF50;
        }
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #333;
            border-radius: 4px;
            background-color: #2d2d2d;
            color: white;
        }
        .admin-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            margin-right: 10px;
        }
        .admin-btn:hover {
            background-color: #45a049;
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
            margin-top: 10px;
        }
        .success {
            background-color: #263238;
            color: #4CAF50;
            border: 1px solid #4CAF50;
        }
        .error {
            background-color: #263238;
            color: #f44336;
            border: 1px solid #f44336;
        }
        h2 {
            color: #4CAF50;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <% if (success != null) { %>
            <div class="message success">
                <%= success %>
            </div>
        <% } else if (error != null) { %>
            <div class="message error">
                <%= error %>
            </div>
        <% } %>
        
        <h2>Modifica Utente</h2>
        <form method="post">
            <input type="hidden" name="username" value="<%= username %>">
            <input type="hidden" name="action" value="update">
            
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" value="<%= rs.getString("nome") %>" required>
            </div>
            
            <div class="form-group">
                <label for="cognome">Cognome:</label>
                <input type="text" id="cognome" name="cognome" value="<%= rs.getString("cognome") %>" required>
            </div>
            
            <div class="form-group">
                <label for="telefono">Telefono:</label>
                <input type="tel" id="telefono" name="telefono" value="<%= rs.getString("telefono") %>">
            </div>
            
            <div class="form-group">
                <label for="codice_fiscale">Codice Fiscale:</label>
                <input type="text" id="codice_fiscale" name="codice_fiscale" value="<%= rs.getString("codice_fiscale") %>">
            </div>
            
            <div class="form-group">
                <label for="ruolo">Ruolo:</label>
                <select id="ruolo" name="ruolo">
                    <option value="utente" <%= rs.getString("ruolo").equals("utente") ? "selected" : "" %>>Utente</option>
                    <option value="admin" <%= rs.getString("ruolo").equals("admin") ? "selected" : "" %>>Admin</option>
                </select>
            </div>
            
            <div class="form-group">
                <input type="submit" value="Salva Modifiche" class="admin-btn">
                <a href="area_amministratore.jsp" class="admin-btn">Annulla</a>
            </div>
        </form>
    </div>
</body>
</html>
<%
        } else {
            String nome = request.getParameter("nome");
            String cognome = request.getParameter("cognome");
            String telefono = request.getParameter("telefono");
            String codice_fiscale = request.getParameter("codice_fiscale");
            String ruolo = request.getParameter("ruolo");
            
            String sql = "UPDATE utenti SET nome = ?, cognome = ?, telefono = ?, codice_fiscale = ?, ruolo = ? WHERE username = ?";
            pstmt = conn.prepareStatement(sql);
            
            pstmt.setString(1, nome);
            pstmt.setString(2, cognome);
            pstmt.setString(3, telefono);
            pstmt.setString(4, codice_fiscale);
            pstmt.setString(5, ruolo);
            pstmt.setString(6, username);
            
            int rowsAffected = pstmt.executeUpdate();
            
            if (rowsAffected > 0) {
                response.sendRedirect("area_amministratore.jsp?success=Utente aggiornato con successo");
            } else {
                response.sendRedirect("area_amministratore.jsp?error=Errore durante l'aggiornamento");
            }
        }
    } catch (Exception e) {
        response.sendRedirect("area_amministratore.jsp?error=" + e.getMessage());
    } finally {
        try { 
            if (rs != null) rs.close(); 
        } catch (Exception e) {}
        try { 
            if (checkStmt != null) checkStmt.close(); 
        } catch (Exception e) {}
        try { 
            if (pstmt != null) pstmt.close(); 
        } catch (Exception e) {}
        try { 
            if (conn != null) conn.close(); 
        } catch (Exception e) {}
    }
%>
