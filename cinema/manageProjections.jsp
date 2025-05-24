<!DOCTYPE html>
<%@ page language="java" contentType="text/html; charset=UTF-8" pageEncoding="UTF-8"%>
<html>
<head>
<meta charset="UTF-8">
<title>Gestione Proiezioni</title>
</head>
<body>
<%@ page import="java.sql.Connection" %>
<%@ page import="java.sql.DriverManager" %>
<%@ page import="java.sql.PreparedStatement" %>
<%@ page import="java.sql.SQLException" %>
<%@ page import="java.sql.ResultSet" %>

<%
    HttpSession userSession = request.getSession(false);
    if (userSession == null || userSession.getAttribute("username") == null) {
        response.sendRedirect("index.jsp");
        return;
    }
    
    String username = (String) userSession.getAttribute("username");
    String role = (String) userSession.getAttribute("role");
    if (!"admin".equals(role)) {
        response.sendRedirect("area_riservata.jsp");
        return;
    }

    String filmCode = request.getParameter("filmCode");
    String date = request.getParameter("date");
    String time = request.getParameter("time");
    String room = request.getParameter("room");
    if (room == null || room.isEmpty()) {
        room = "6";
    }

    try {
        Class.forName("com.mysql.cj.jdbc.Driver");
        Connection conn = DriverManager.getConnection("jdbc:mysql://localhost:3306/cinema", "root", "");
        
        // Verifica se il campo Sala esiste
        String checkSql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'proiezioni' AND COLUMN_NAME = 'Sala'";
        PreparedStatement checkStmt = conn.prepareStatement(checkSql);
        ResultSet rs = checkStmt.executeQuery();
        
        // Se non esiste, crea il campo Sala
        if (!rs.next()) {
            String createSql = "ALTER TABLE proiezioni ADD COLUMN Sala INT DEFAULT 6";
            PreparedStatement createStmt = conn.prepareStatement(createSql);
            createStmt.executeUpdate();
        }

        // Verifica se esiste la colonna Cod_proiezione (da rimuovere)
        String checkOldColumnSql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'proiezioni' AND COLUMN_NAME = 'Cod_proiezione'";
        PreparedStatement checkOldColumnStmt = conn.prepareStatement(checkOldColumnSql);
        ResultSet oldColumnRs = checkOldColumnStmt.executeQuery();
        
        if (oldColumnRs.next()) {
            // Se esiste la vecchia colonna, la rimuoviamo
            String dropColumnSql = "ALTER TABLE proiezioni DROP COLUMN Cod_proiezione";
            PreparedStatement dropColumnStmt = conn.prepareStatement(dropColumnSql);
            dropColumnStmt.executeUpdate();
            out.println("<div class=\"info-message\">Rimossa vecchia colonna Cod_proiezione</div>");
        }

        // Verifica se esiste la colonna Codice_proiezioni
        String checkNewColumnSql = "SELECT COLUMN_NAME, EXTRA FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'proiezioni' AND COLUMN_NAME = 'Codice_proiezioni'";
        PreparedStatement checkNewColumnStmt = conn.prepareStatement(checkNewColumnSql);
        ResultSet newColumnRs = checkNewColumnStmt.executeQuery();
        
        if (!newColumnRs.next()) {
            // Se non esiste, la creiamo come auto_increment
            String createColumnSql = "ALTER TABLE proiezioni ADD COLUMN Codice_proiezioni INT AUTO_INCREMENT PRIMARY KEY";
            PreparedStatement createColumnStmt = conn.prepareStatement(createColumnSql);
            createColumnStmt.executeUpdate();
            out.println("<div class=\"info-message\">Creata nuova colonna Codice_proiezioni come auto_increment</div>");
        } else {
            String extra = newColumnRs.getString("EXTRA");
            if (extra == null || !extra.contains("auto_increment")) {
                // Se esiste ma non è auto_increment, la modifichiamo
                String modifyColumnSql = "ALTER TABLE proiezioni MODIFY COLUMN Codice_proiezioni INT AUTO_INCREMENT PRIMARY KEY";
                PreparedStatement modifyColumnStmt = conn.prepareStatement(modifyColumnSql);
                modifyColumnStmt.executeUpdate();
                out.println("<div class=\"info-message\">Modificata colonna Codice_proiezioni come auto_increment</div>");
            }
        }

        // Prosegui con l'inserimento
        String sql = "INSERT INTO proiezioni (Codice_film, Data, Ora, Sala) VALUES (?, ?, ?, ?)";
        PreparedStatement stmt = conn.prepareStatement(sql, PreparedStatement.RETURN_GENERATED_KEYS);
        stmt.setString(1, filmCode);
        stmt.setString(2, date);
        stmt.setString(3, time);
        stmt.setString(4, room);
        
        int rows = stmt.executeUpdate();
        
        if (rows > 0) {
            ResultSet generatedKeys = stmt.getGeneratedKeys();
            if (generatedKeys.next()) {
                int codiceProiezione = generatedKeys.getInt(1);
                out.println("<div class=\"info-message\">Proiezione inserita con codice: " + codiceProiezione + "</div>");
            }
            response.sendRedirect("area_amministratore.jsp");
        } else {
            out.println("Errore nell'inserimento della proiezione.");
        }
        
        stmt.close();
        conn.close();
    } catch (Exception e) {
        out.println("Errore: " + e.getMessage());
    }
%>
</body>
</html>
