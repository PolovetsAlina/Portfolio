<%@ page language="java" contentType="text/html; charset=UTF-8" pageEncoding="UTF-8"%>
<%@ page import="java.sql.Connection" %>
<%@ page import="java.sql.DriverManager" %>
<%@ page import="java.sql.PreparedStatement" %>
<%@ page import="java.sql.ResultSet" %>
<%@ page import="java.sql.SQLException" %>

<% 
    // Session check
    if (session == null || session.getAttribute("username") == null) {
        response.sendRedirect("index.jsp");
        return;
    }
    
    // Permission check
    String role = (String) session.getAttribute("role");
    if (!"admin".equals(role)) {
        response.sendRedirect("area_riservata.jsp");
        return;
    }

    String code = request.getParameter("code");
    if (code == null || code.trim().isEmpty()) {
        session.setAttribute("error", "Codice film mancante");
        response.sendRedirect("area_amministratore.jsp");
        return;
    }
    code = code.trim().toUpperCase();
    
    Connection conn = null;
    PreparedStatement pstmt = null;
    ResultSet rs = null;
    
    String errorMessage = (String) session.getAttribute("error");
    session.removeAttribute("error");
    String successMessage = (String) session.getAttribute("success");
    session.removeAttribute("success");
%>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Film - Cinema Hub</title>
    <link rel="stylesheet" href="index.css?v=<%= new java.util.Date().getTime() %>">
    <style>
        body {
            background-color: #1a1a1a;
            color: #e0e0e0;
            font-family: Arial, sans-serif;
        }
        
        .error-message {
            color: #ff6b6b;
            background-color: #2e1d1d;
            padding: 12px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #ff6b6b;
        }
        
        .success-message {
            color: #6bff6b;
            background-color: #1d2e1d;
            padding: 12px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #6bff6b;
        }
        
        .admin-panel {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
        }
        
        .admin-form {
            background: #2d2d2d;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #b8b8b8;
        }
        
        .form-group input, 
        .form-group select {
            width: 100%;
            padding: 12px;
            background: #3d3d3d;
            border: 1px solid #4d4d4d;
            border-radius: 6px;
            color: #e0e0e0;
            font-size: 16px;
        }
        
        .form-group input:focus {
            border-color: #007bff;
            outline: none;
        }
        
        .current-image {
            max-width: 250px;
            margin: 15px 0;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .admin-btn {
            padding: 12px 24px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
            transition: background-color 0.3s;
        }
        
        .admin-btn:hover {
            background-color: #0069d9;
        }
        
        .btn-cancel {
            background-color: #6c757d;
        }
        
        .btn-cancel:hover {
            background-color: #5a6268;
        }
        
        .small-text {
            font-size: 13px;
            color: #999;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <main class="admin-panel">
        <h1>Modifica Film</h1>
        
        <% if (errorMessage != null) { %>
            <div class="error-message">
                <%= errorMessage %>
            </div>
        <% } %>
        
        <% if (successMessage != null) { %>
            <div class="success-message">
                <%= successMessage %>
            </div>
        <% } %>
        
        <% 
            try {
                Class.forName("com.mysql.cj.jdbc.Driver");
                conn = DriverManager.getConnection("jdbc:mysql://localhost:3306/cinema", "root", "");

                // Verifica che il codice film esista nel database
                String checkSql = "SELECT COUNT(*) FROM film WHERE Codice_film = ?";
                PreparedStatement checkStmt = conn.prepareStatement(checkSql);
                checkStmt.setString(1, code);
                ResultSet checkRs = checkStmt.executeQuery();
                checkRs.next();
                if (checkRs.getInt(1) == 0) {
                    session.setAttribute("error", "Il codice film specificato non esiste nel database");
                    response.sendRedirect("area_amministratore.jsp");
                    return;
                }

                String sql = "SELECT * FROM film WHERE Codice_film = ?";
                pstmt = conn.prepareStatement(sql);
                pstmt.setString(1, code);
                rs = pstmt.executeQuery();
                
                if (rs.next()) {
        %>
        <div class="admin-form">
            <% 
                // Memorizza il codice film in sessione
                session.setAttribute("currentFilmCode", code);
            %>
            <form method="post" action="updateMovie.jsp" enctype="multipart/form-data">
                <input type="hidden" name="filmCode" value="<%= code %>">
                
                <div class="form-group">
                    <label for="title">Titolo</label>
                    <input type="text" id="title" name="title" value="<%= rs.getString("Titolo") %>" required>
                </div>
                
                <div class="form-group">
                    <label for="director">Regista</label>
                    <input type="text" id="director" name="director" value="<%= rs.getString("Regista") %>" required>
                </div>
                
                <div class="form-group">
                    <label for="year">Anno</label>
                    <input type="number" id="year" name="year" min="1900" max="<%= new java.util.Date().getYear() + 1900 %>" 
                           value="<%= rs.getString("Anno") %>" required>
                </div>
                
                <div class="form-group">
                    <label>Immagine attuale</label>
                    <img src="img/<%= rs.getString("Immagine") %>" class="current-image" alt="Immagine del film">
                </div>
                
                <div class="form-group">
                    <label for="newImage">Nuova immagine</label>
                    <input type="file" id="newImage" name="newImage" accept="image/*">
                    <p class="small-text">Lascia vuoto per mantenere l'immagine attuale</p>
                </div>
                
                <button type="submit" class="admin-btn">Salva Modifiche</button>
                <a href="area_amministratore.jsp" class="admin-btn btn-cancel">Annulla</a>
            </form>
        </div>
        <% 
                } else {
                    errorMessage = "Film non trovato con codice: " + code;
                }
            } catch(Exception e) {
                errorMessage = "Errore nel caricamento del film: " + e.getMessage();
            } finally {
                try { if (rs != null) rs.close(); } catch (Exception e) { }
                try { if (pstmt != null) pstmt.close(); } catch (Exception e) { }
                try { if (conn != null) conn.close(); } catch (Exception e) { }
            }
        %>
    </main>
</body>
</html>
