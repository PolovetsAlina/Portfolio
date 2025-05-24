<%@ page language="java" contentType="text/html; charset=UTF-8" pageEncoding="UTF-8"%>
<%@ page import="java.sql.Connection" %>
<%@ page import="java.sql.DriverManager" %>
<%@ page import="java.sql.PreparedStatement" %>
<%@ page import="java.sql.SQLException" %>
<%@ page import="javax.servlet.http.HttpSession" %>

<%
    // Verifica se l'utente è loggato
    if (session == null || session.getAttribute("username") == null) {
        response.sendRedirect("index.jsp");
        return;
    }
    
    String username = (String) session.getAttribute("username");
    String role = (String) session.getAttribute("role");
    if (!"admin".equals(role)) {
        response.sendRedirect("area_riservata.jsp");
        return;
    }

    String codiceProiezioni = request.getParameter("Codice_proiezioni");
    String codiceFilm = request.getParameter("Codice_film");
    String data = request.getParameter("Data");
    String ora = request.getParameter("Ora");

    try {
        Class.forName("com.mysql.cj.jdbc.Driver");
        Connection conn = DriverManager.getConnection("jdbc:mysql://localhost:3306/cinema", "root", "");

        String sql = "UPDATE proiezioni SET Codice_film = ?, Data = ?, Ora = ? WHERE Codice_proiezioni = ?";
        PreparedStatement stmt = conn.prepareStatement(sql);
        stmt.setString(1, codiceFilm);
        stmt.setString(2, data);
        stmt.setString(3, ora);
        stmt.setString(4, codiceProiezioni);
        
        int rows = stmt.executeUpdate();
        
        if (rows > 0) {
            response.sendRedirect("area_amministratore.jsp");
        } else {
            out.println("Errore nell'aggiornamento della proiezione.");
        }
        
        stmt.close();
        conn.close();
    } catch (Exception e) {
        out.println("Errore: " + e.getMessage());
    }
%>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Aggiornamento Proiezione</title>
</head>
<body>
    <div style="text-align: center; margin-top: 50px;">
        <p>La proiezione è stata aggiornata con successo!</p>
        <p>Verrai reindirizzato alla pagina amministratore...</p>
    </div>
</body>
</html>
